<?php

namespace App\Livewire\Mobile\Guru;

use App\Models\Master\MasterJadwalPelajaran;
use App\Models\JurnalGuru;
use App\Models\AbsensiGuru;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardGuru extends Component
{
    public $jadwalHariIni = [];
    public $absenMasuk = null;
    public $absenPulang = null;
    public $totalJam = 0;
    public $totalTerisi = 0;

    public function mount()
    {
        $guruId = Auth::user()->guru_id;
        if (!$guruId) {
            $guruId = Auth::user()->guru->id ?? null; // Adjust based on how User is linked to Guru
        }

        if (!$guruId) return;

        $hariIniStr = Carbon::now()->locale('id')->isoFormat('dddd');
        $tanggalSekarang = Carbon::today()->format('Y-m-d');

        // Get Absensi
        $this->absenMasuk = AbsensiGuru::where('guru_id', $guruId)
            ->where('tanggal', $tanggalSekarang)
            ->where('jenis', 'masuk')
            ->first();
            
        $this->absenPulang = AbsensiGuru::where('guru_id', $guruId)
            ->where('tanggal', $tanggalSekarang)
            ->where('jenis', 'pulang')
            ->first();

        // Get Jadwal
        $jadwal = MasterJadwalPelajaran::with(['kelas', 'mapel'])
            ->where('guru_id', $guruId)
            ->where('hari', $hariIniStr)
            ->orderBy('jam_mulai')
            ->get();

        $this->totalJam = $jadwal->count();

        // Get Jurnal for today
        $jurnalFilled = JurnalGuru::where('guru_id', $guruId)
            ->where('tanggal', $tanggalSekarang)
            ->where('status', 'diisi')
            ->pluck('jam_ke')
            ->toArray();

        $this->totalTerisi = count($jurnalFilled);

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

    public function render()
    {
        return view('livewire.mobile.guru.dashboard-guru')->layout('components.layouts.pwa');
    }
}
