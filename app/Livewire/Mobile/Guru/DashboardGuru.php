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
    public ?int $persentaseKehadiran = null; // null = belum ada hari kerja yang dinilai

    public int $hariHadir = 0;

    public int $hariEfektif = 0;
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

        // 2. Persentase kehadiran = hari hadir (tepat/terlambat/dinas luar) ÷ hari kerja efektif.
        //    Hari kerja tanpa catatan absen = tidak hadir (konsisten dengan rekap bulanan).
        //    Dihitung sejak sistem absensi dipakai (bukan awal TA) agar tidak terhukum oleh masa sebelum aplikasi ada;
        //    hari ini hanya dihitung bila sudah absen masuk.
        $tahunAjaran = MasterTahunAjaran::where('is_active', true)->first();
        $this->persentaseKehadiran = null;
        $mulaiSistem = AbsensiGuru::min('tanggal');
        if ($tahunAjaran && $tahunAjaran->mulai && $mulaiSistem) {
            $startDate = Carbon::parse($tahunAjaran->mulai)->max(Carbon::parse($mulaiSistem))->startOfDay();
            $endDate = $this->absenMasuk ? Carbon::today() : Carbon::yesterday();
            if ($tahunAjaran->selesai && Carbon::parse($tahunAjaran->selesai)->lt($endDate)) {
                $endDate = Carbon::parse($tahunAjaran->selesai);
            }

            if ($startDate->lte($endDate)) {
                $this->hariEfektif = HolidayService::getEffectiveWorkDays($startDate, $endDate);
                $this->hariHadir = AbsensiGuru::where('guru_id', $guruId)
                    ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
                    ->where('jenis', 'masuk')
                    ->whereIn('status', ['hadir', 'terlambat', 'dinas_luar'])
                    ->distinct()->count('tanggal');

                if ($this->hariEfektif > 0) {
                    $this->persentaseKehadiran = (int) min(100, round($this->hariHadir / $this->hariEfektif * 100));
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

        // 4. Jadwal hari ini (jadwal v2 — memperhitungkan upacara & pengecualian)
        $harian = app(\App\Services\JadwalService::class)->jadwalGuruHarian($guruId, Carbon::today());
        // Satu kartu per pertemuan (blok yang hanya dipisah istirahat digabung)
        $this->jadwalHariIni = collect($harian['items'])
            ->where('tipe', 'blok')
            ->groupBy(fn ($b) => implode(',', $b['jurnal_sesi_ids']))
            ->map(function ($grup) {
                $b = $grup->first();

                return [
                    'sesi_ids' => $b['jurnal_sesi_ids'],
                    'jam_ke' => $b['jam_ke'][0],
                    'jam_mulai' => $b['mulai'],
                    'jam_selesai' => $grup->last()['selesai'],
                    'kelas' => implode(' + ', $b['kelas']),
                    'mapel' => $b['mapel'],
                    'status' => match ($b['jurnal']) { 'diisi' => 'diisi', 'tidak_wajib' => 'tidak_wajib', default => 'belum_diisi' },
                    'catatan' => $b['catatan'] ?? null,
                ];
            })
            ->values()
            ->all();
    }

    // prosesAbsensiBg() dihapus: method public Livewire bisa dipanggil langsung dari browser dan
    // mencatat absen tanpa validasi radius, swafoto, maupun jendela waktu (AturanAbsensiService).

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
        return view('livewire.mobile.guru.dashboard-guru')
            ->layout('components.layouts.pwa', ['tanpaHeader' => true, 'judul' => 'Beranda']);
    }
}
