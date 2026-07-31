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

        $processedJadwal = collect();
        $lastGroup = null;

        foreach ($jadwalList as $jadwal) {
            // Check status for this specific JP
            $status = 'mendatang';
            $jurnalRef = null;
            if (strtolower($jadwal->hari) == $this->hariIni) {
                if (isset($jurnalHariIni[$jadwal->jam_ke]) && $jurnalHariIni[$jadwal->jam_ke]->status == 'diisi') {
                    $status = 'sudah_diisi';
                    $jurnalRef = $jurnalHariIni[$jadwal->jam_ke];
                } else {
                    $status = 'belum_diisi';
                }
            }

            if ($lastGroup && $lastGroup->hari == $jadwal->hari && $lastGroup->kelas_id == $jadwal->kelas_id && $lastGroup->mapel_id == $jadwal->mapel_id) {
                // Append to previous group
                $lastGroup->jam_selesai = $jadwal->jam_selesai;
                
                $ids = $lastGroup->jadwal_ids;
                $ids[] = $jadwal->id;
                $lastGroup->jadwal_ids = $ids;
                
                if ($status == 'sudah_diisi') {
                    $lastGroup->status_jurnal = 'sudah_diisi';
                    $lastGroup->jurnal_ref = $jurnalRef;
                } elseif ($status == 'belum_diisi' && $lastGroup->status_jurnal == 'mendatang') {
                    $lastGroup->status_jurnal = 'belum_diisi';
                }
                
                if (strtolower($jadwal->hari) == $this->hariIni && $currentTime >= $jadwal->jam_mulai && $currentTime <= $jadwal->jam_selesai) {
                    $sesiAktif = $lastGroup;
                }
            } else {
                if ($lastGroup) {
                    $processedJadwal->push($lastGroup);
                }
                
                $lastGroup = clone $jadwal;
                $lastGroup->jadwal_ids = [$jadwal->id];
                $lastGroup->status_jurnal = $status;
                $lastGroup->jurnal_ref = $jurnalRef;
                
                if (strtolower($jadwal->hari) == $this->hariIni && $currentTime >= $jadwal->jam_mulai && $currentTime <= $jadwal->jam_selesai) {
                    $sesiAktif = $lastGroup;
                }
            }
        }
        if ($lastGroup) {
            $processedJadwal->push($lastGroup);
        }

        if ($this->mode === 'mingguan') {
            $processedJadwal = $processedJadwal->groupBy('hari');
        }

        return view('livewire.mobile.guru.jadwal-guru', [
            'jadwals' => $processedJadwal,
            'sesiAktif' => $sesiAktif,
        ]);
    }
}
