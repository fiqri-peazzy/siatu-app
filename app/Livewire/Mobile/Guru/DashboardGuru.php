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
    public $absensiHariIni = null;
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
        $this->absensiHariIni = AbsensiGuru::where('guru_id', $guruId)
            ->where('tanggal', $tanggalSekarang)
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

        foreach ($jadwal as $j) {
            $isFilled = in_array($j->jam_ke, $jurnalFilled);
            $this->jadwalHariIni[] = [
                'id' => $j->id,
                'jam_ke' => $j->jam_ke,
                'jam_mulai' => Carbon::parse($j->jam_mulai)->format('H:i'),
                'jam_selesai' => Carbon::parse($j->jam_selesai)->format('H:i'),
                'kelas' => $j->kelas ? $j->kelas->nama_kelas : '-',
                'mapel' => $j->mapel ? $j->mapel->nama_mapel : '-',
                'status' => $isFilled ? 'diisi' : 'belum_diisi',
            ];
        }
    }

    public function render()
    {
        return view('livewire.mobile.guru.dashboard-guru')->layout('components.layouts.pwa');
    }
}
