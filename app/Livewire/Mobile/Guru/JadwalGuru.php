<?php

namespace App\Livewire\Mobile\Guru;

use App\Models\Master\MasterJadwalPelajaran;
use App\Models\JurnalGuru;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.pwa')]
class JadwalGuru extends Component
{
    public $mode = 'harian'; // 'harian' atau 'mingguan'
    public $tanggalAktif;
    public $hariIni;

    public function mount()
    {
        $this->tanggalAktif = Carbon::now()->format('Y-m-d');
        $this->hariIni = strtolower(Carbon::now()->locale('id')->isoFormat('dddd'));
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function render()
    {
        $guruId = Auth::user()->guru_id ?? Auth::user()->guru->id ?? null;

        // Ambil jadwal guru
        $queryJadwal = MasterJadwalPelajaran::with(['kelas', 'mapel'])
            ->where('guru_id', $guruId)
            ->orderBy('jam_ke');

        if ($this->mode === 'harian') {
            $queryJadwal->where('hari', $this->hariIni);
        } else {
            $queryJadwal->orderByRaw("FIELD(hari, 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu')");
        }

        $jadwalList = $queryJadwal->get();

        // Ambil jurnal yang sudah diisi hari ini
        $jurnalHariIni = JurnalGuru::where('guru_id', $guruId)
            ->where('tanggal', $this->tanggalAktif)
            ->get()
            ->keyBy('jam_ke');

        $currentTime = Carbon::now()->format('H:i:s');
        $sesiAktif = null;

        // Process jadwal
        $processedJadwal = $jadwalList->map(function($jadwal) use ($jurnalHariIni, $currentTime, &$sesiAktif) {
            // Check status
            $status = 'mendatang';
            if ($jadwal->hari == $this->hariIni) {
                if (isset($jurnalHariIni[$jadwal->jam_ke]) && $jurnalHariIni[$jadwal->jam_ke]->status == 'diisi') {
                    $status = 'sudah_diisi';
                } elseif ($currentTime >= $jadwal->jam_mulai) {
                    $status = 'belum_diisi';
                }
            } else {
                $status = 'mendatang'; // Untuk mingguan, asumsikan mendatang kecuali jika logika hari ini
            }

            // Check if active session
            if ($jadwal->hari == $this->hariIni && $currentTime >= $jadwal->jam_mulai && $currentTime <= $jadwal->jam_selesai) {
                $sesiAktif = $jadwal;
            }

            $jadwal->status_jurnal = $status;
            $jadwal->jurnal_ref = $jurnalHariIni[$jadwal->jam_ke] ?? null;
            return $jadwal;
        });

        if ($this->mode === 'mingguan') {
            $processedJadwal = $processedJadwal->groupBy('hari');
        }

        return view('livewire.mobile.guru.jadwal-guru', [
            'jadwals' => $processedJadwal,
            'sesiAktif' => $sesiAktif,
        ]);
    }
}
