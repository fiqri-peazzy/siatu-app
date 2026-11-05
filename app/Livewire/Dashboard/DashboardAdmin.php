<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardService;
use Livewire\Attributes\Layout;
use Livewire\Component;

/** Dashboard operasional Admin TU / Super Admin: antrian surat, data master, kelengkapan, absensi. */
#[Layout('components.layouts.app')]
class DashboardAdmin extends Component
{
    public function render(DashboardService $d)
    {
        return view('livewire.dashboard.dashboard-admin', [
            'guru' => $d->kehadiranPegawai('guru'),
            'tendik' => $d->kehadiranPegawai('tendik'),
            'siswa' => $d->kehadiranSiswa(),
            'kbm' => $d->kbm(),
            'surat' => $d->surat(),
            'data' => $d->kelengkapanData(),
        ]);
    }
}
