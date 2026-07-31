<?php

namespace App\Livewire\Mobile\Guru;

use App\Models\Master\MasterJadwalPelajaran;
use App\Models\Master\MasterTahunAjaran;
use App\Models\JurnalGuru;
use App\Models\AbsensiGuru;
use App\Services\HolidayService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardGuru extends Component
{
    public $jadwalHariIni = [];
    public $absenMasuk = null;
    public $absenPulang = null;
    public $persentaseKehadiran = 100;
    public $totalJurnalTerisi = 0;

    public $calendarMonth;
    public $calendarYear;
    
    public $calendarHolidays = [];
    public $teacherScheduleDays = [];
    public $selectedDate = null;
    public $selectedDateDetails = null;

    public function mount()
    {
        $this->calendarMonth = Carbon::now()->month;
        $this->calendarYear = Carbon::now()->year;
        $this->loadCalendarData();

        $guruId = Auth::user()->guru_id;
        if (!$guruId) {
            $guruId = Auth::user()->guru->id ?? null; // Adjust based on how User is linked to Guru
        }

        if (!$guruId) return;

        $hariIniStr = strtolower(Carbon::now()->locale('id')->isoFormat('dddd'));
        $tanggalSekarang = Carbon::today()->format('Y-m-d');

        // 1. Get Absensi Hari Ini
        $this->absenMasuk = AbsensiGuru::where('guru_id', $guruId)
            ->where('tanggal', $tanggalSekarang)
            ->where('jenis', 'masuk')
            ->first();
            
        $this->absenPulang = AbsensiGuru::where('guru_id', $guruId)
            ->where('tanggal', $tanggalSekarang)
            ->where('jenis', 'pulang')
            ->first();

        // 2. Hitung Persentase Kehadiran
        $tahunAjaran = MasterTahunAjaran::where('is_active', true)->first();
        if ($tahunAjaran && $tahunAjaran->mulai) {
            $startDate = Carbon::parse($tahunAjaran->mulai);
            $endDate = Carbon::today();
            if ($tahunAjaran->selesai && Carbon::parse($tahunAjaran->selesai)->lt($endDate)) {
                $endDate = Carbon::parse($tahunAjaran->selesai);
            }

            if ($startDate->lte($endDate)) {
                $totalHariEfektif = HolidayService::getEffectiveWorkDays($startDate, $endDate);
                
                // Hitung total alfa guru tersebut dalam rentang waktu yang sama
                $totalAlfa = AbsensiGuru::where('guru_id', $guruId)
                    ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->where('status', 'alfa')
                    ->where('jenis', 'masuk') // Alfa biasanya dicatat di jenis masuk (harian)
                    ->count();

                if ($totalHariEfektif > 0) {
                    // Rumus: (Total Hari - Alfa) / Total Hari * 100
                    $persentase = (($totalHariEfektif - $totalAlfa) / $totalHariEfektif) * 100;
                    // Pastikan tidak minus
                    $this->persentaseKehadiran = max(0, round($persentase));
                }
            }
        }

        // 3. Hitung Jurnal Terisi selama Tahun Ajaran Aktif
        if ($tahunAjaran && $tahunAjaran->mulai) {
            $startDate = Carbon::parse($tahunAjaran->mulai)->format('Y-m-d');
            $endDate = $tahunAjaran->selesai ? Carbon::parse($tahunAjaran->selesai)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
            
            // Jurnal dihitung per-pertemuan (sesi) unik (per guru, per tanggal, per mapel, per kelas)
            // Karena satu pertemuan mungkin terdiri dari beberapa baris JP di database, kita count unique group
            $jurnalUnik = JurnalGuru::where('guru_id', $guruId)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->where('status', 'diisi')
                ->select('tanggal', 'kelas_id', 'mapel_id')
                ->groupBy('tanggal', 'kelas_id', 'mapel_id')
                ->get();
                
            $this->totalJurnalTerisi = $jurnalUnik->count();
        } else {
            $this->totalJurnalTerisi = 0;
        }

        // 4. Get Jadwal Hari Ini
        $jadwal = MasterJadwalPelajaran::with(['kelas', 'mapel'])
            ->where('guru_id', $guruId)
            ->where('hari', $hariIniStr)
            ->orderBy('jam_mulai')
            ->get();

        $jurnalFilled = JurnalGuru::where('guru_id', $guruId)
            ->where('tanggal', $tanggalSekarang)
            ->where('status', 'diisi')
            ->pluck('jam_ke')
            ->toArray();

        $groupedJadwal = [];
        $lastGroup = null;

        foreach ($jadwal as $j) {
            $isFilled = in_array($j->jam_ke, $jurnalFilled);
            
            if ($lastGroup && $lastGroup['kelas'] == ($j->kelas->nama ?? '-') && $lastGroup['mapel'] == ($j->mapel->nama ?? '-')) {
                // Group with previous
                $lastGroup['jam_selesai'] = Carbon::parse($j->jam_selesai)->format('H:i');
                if ($isFilled) $lastGroup['status'] = 'diisi';
                $lastGroup['jadwal_ids'][] = $j->id;
            } else {
                if ($lastGroup) $groupedJadwal[] = $lastGroup;
                
                $lastGroup = [
                    'id' => $j->id,
                    'jadwal_ids' => [$j->id],
                    'jam_ke' => $j->jam_ke,
                    'jam_mulai' => Carbon::parse($j->jam_mulai)->format('H:i'),
                    'jam_selesai' => Carbon::parse($j->jam_selesai)->format('H:i'),
                    'kelas' => $j->kelas ? $j->kelas->nama : '-',
                    'mapel' => $j->mapel ? $j->mapel->nama : '-',
                    'status' => $isFilled ? 'diisi' : 'belum_diisi',
                ];
            }
        }
        if ($lastGroup) {
            $groupedJadwal[] = $lastGroup;
        }

        $this->jadwalHariIni = $groupedJadwal;
    }

    public function prosesAbsensiBg($lat, $lng)
    {
        $guruId = Auth::user()->guru_id ?? Auth::user()->guru->id ?? null;
        if (!$guruId) return;

        $tanggalSekarang = Carbon::today()->format('Y-m-d');
        $waktuAbsen = Carbon::now();

        // Refresh status
        $this->absenMasuk = AbsensiGuru::where('guru_id', $guruId)
            ->where('tanggal', $tanggalSekarang)
            ->where('jenis', 'masuk')
            ->first();

        $this->absenPulang = AbsensiGuru::where('guru_id', $guruId)
            ->where('tanggal', $tanggalSekarang)
            ->where('jenis', 'pulang')
            ->first();

        if (!$this->absenMasuk) {
            // Proses Absen Masuk
            AbsensiGuru::create([
                'guru_id' => $guruId,
                'tanggal' => $tanggalSekarang,
                'jenis' => 'masuk',
                'status' => 'hadir',
                'waktu_absen' => $waktuAbsen,
                'latitude' => $lat,
                'longitude' => $lng,
            ]);

            $this->dispatch('swal:success', ['Absen Masuk berhasil dicatat!']);
        } elseif (!$this->absenPulang) {
            // Proses Absen Pulang
            AbsensiGuru::create([
                'guru_id' => $guruId,
                'tanggal' => $tanggalSekarang,
                'jenis' => 'pulang',
                'status' => 'hadir',
                'waktu_absen' => $waktuAbsen,
                'latitude' => $lat,
                'longitude' => $lng,
            ]);
            
            $this->dispatch('swal:success', ['Absen Pulang berhasil dicatat!']);
        } else {
            $this->dispatch('swal:error', ['Anda sudah melakukan absen masuk dan pulang hari ini.']);
        }

        // Reload data
        $this->mount();
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->addMonth();
        $this->calendarMonth = $date->month;
        $this->calendarYear = $date->year;
        $this->loadCalendarData();
    }

    public function prevMonth()
    {
        $date = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->subMonth();
        $this->calendarMonth = $date->month;
        $this->calendarYear = $date->year;
        $this->loadCalendarData();
    }

    public function loadCalendarData()
    {
        $this->selectedDate = null;
        $this->selectedDateDetails = null;

        $guruId = Auth::user()->guru_id ?? Auth::user()->guru->id ?? null;
        if (!$guruId) return;

        $this->calendarHolidays = HolidayService::getHolidays($this->calendarYear);

        // Fetch the distinct days the teacher has classes
        $schedules = MasterJadwalPelajaran::where('guru_id', $guruId)
            ->select('hari')
            ->distinct()
            ->pluck('hari')
            ->toArray();
            
        // Map Indonesian days to Carbon day of week index (0=Sunday, 1=Monday...)
        $dayMap = [
            'senin' => 1,
            'selasa' => 2,
            'rabu' => 3,
            'kamis' => 4,
            'jumat' => 5,
            'sabtu' => 6,
            'minggu' => 0,
        ];

        $this->teacherScheduleDays = [];
        foreach ($schedules as $hari) {
            $hariLower = strtolower($hari);
            if (isset($dayMap[$hariLower])) {
                $this->teacherScheduleDays[] = $dayMap[$hariLower];
            }
        }
    }

    public function selectDate($dateStr)
    {
        $this->selectedDate = $dateStr;
        $date = Carbon::parse($dateStr);
        $hari = strtolower($date->locale('id')->isoFormat('dddd'));
        
        $details = [
            'is_holiday' => false,
            'holiday_name' => null,
            'has_schedule' => false,
            'schedules' => []
        ];

        if (array_key_exists($dateStr, $this->calendarHolidays)) {
            $details['is_holiday'] = true;
            $details['holiday_name'] = $this->calendarHolidays[$dateStr];
        }

        $guruId = Auth::user()->guru_id ?? Auth::user()->guru->id ?? null;
        if ($guruId && !$details['is_holiday']) {
            $schedules = MasterJadwalPelajaran::with(['kelas', 'mapel'])
                ->where('guru_id', $guruId)
                ->where('hari', $hari)
                ->orderBy('jam_mulai')
                ->get();
            
            if ($schedules->count() > 0) {
                $details['has_schedule'] = true;
                
                // Group them by mapel and kelas for summary
                $grouped = [];
                foreach ($schedules as $s) {
                    $key = $s->kelas_id . '_' . $s->mapel_id;
                    if (!isset($grouped[$key])) {
                        $grouped[$key] = [
                            'kelas' => $s->kelas->nama ?? '-',
                            'mapel' => $s->mapel->nama ?? '-',
                            'jam_mulai' => Carbon::parse($s->jam_mulai)->format('H:i'),
                            'jam_selesai' => Carbon::parse($s->jam_selesai)->format('H:i')
                        ];
                    } else {
                        $grouped[$key]['jam_selesai'] = Carbon::parse($s->jam_selesai)->format('H:i');
                    }
                }
                $details['schedules'] = array_values($grouped);
            }
        }

        $this->selectedDateDetails = $details;
    }

    public function render()
    {
        return view('livewire.mobile.guru.dashboard-guru')->layout('components.layouts.pwa');
    }
}
