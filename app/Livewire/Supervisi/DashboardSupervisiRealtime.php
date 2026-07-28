<?php

namespace App\Livewire\Supervisi;

use Livewire\Component;
use App\Models\Master\MasterJadwalPelajaran;
use App\Models\JurnalGuru;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardSupervisiRealtime extends Component
{
    public $jadwalAktif = [];
    public $ambangWaktu;

    // Refresh every 60 seconds
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $setting = DB::table('pengaturans')->where('key', 'ambang_waktu_jurnal')->first();
        $this->ambangWaktu = $setting ? (int) $setting->value : 30;
    }

    public function render()
    {
        $hariIniStr = Carbon::now()->locale('id')->isoFormat('dddd');
        $tanggalSekarang = Carbon::today()->format('Y-m-d');
        $waktuSekarang = Carbon::now()->format('H:i');

        $jadwals = MasterJadwalPelajaran::with(['guru', 'kelas', 'mapel'])
            ->where('hari', $hariIniStr)
            ->orderBy('jam_mulai')
            ->get();

        $jurnalsDiisi = JurnalGuru::where('tanggal', $tanggalSekarang)
            ->where('status', 'diisi')
            ->get()
            ->keyBy(function($item) {
                return $item->guru_id . '-' . $item->jam_ke;
            });

        $this->jadwalAktif = [];
        foreach ($jadwals as $jadwal) {
            $isFilled = $jurnalsDiisi->has($jadwal->guru_id . '-' . $jadwal->jam_ke);
            
            // Perlu perhatian jika:
            // 1. Belum diisi
            // 2. Waktu sekarang > (jam mulai + ambang batas)
            $perluPerhatian = false;
            if (!$isFilled) {
                $jamMulaiCarbon = Carbon::parse($tanggalSekarang . ' ' . $jadwal->jam_mulai);
                $batasWaktu = $jamMulaiCarbon->copy()->addMinutes($this->ambangWaktu);
                if (Carbon::now()->greaterThan($batasWaktu)) {
                    $perluPerhatian = true;
                }
            }

            // Status: Aktif, Selesai, Menunggu
            $statusKbm = 'menunggu';
            if (Carbon::now()->between(Carbon::parse($tanggalSekarang . ' ' . $jadwal->jam_mulai), Carbon::parse($tanggalSekarang . ' ' . $jadwal->jam_selesai))) {
                $statusKbm = 'aktif';
            } elseif (Carbon::now()->greaterThan(Carbon::parse($tanggalSekarang . ' ' . $jadwal->jam_selesai))) {
                $statusKbm = 'selesai';
            }

            $this->jadwalAktif[] = [
                'id' => $jadwal->id,
                'guru' => $jadwal->guru->nama_guru ?? '-',
                'kelas' => $jadwal->kelas->nama ?? '-',
                'mapel' => $jadwal->mapel->nama_mapel ?? '-',
                'jam_ke' => $jadwal->jam_ke,
                'jam_mulai' => Carbon::parse($jadwal->jam_mulai)->format('H:i'),
                'jam_selesai' => Carbon::parse($jadwal->jam_selesai)->format('H:i'),
                'is_filled' => $isFilled,
                'perlu_perhatian' => $perluPerhatian,
                'status_kbm' => $statusKbm,
            ];
        }

        return view('livewire.supervisi.dashboard-supervisi-realtime')->layout('layouts.app');
    }
}
