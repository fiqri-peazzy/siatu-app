<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardService;
use Livewire\Attributes\Layout;
use Livewire\Component;

/** Dashboard pemantauan Kepala Sekolah: KBM berlangsung, kehadiran, jurnal, perhatian. */
#[Layout('components.layouts.app')]
class DashboardKepsek extends Component
{
    public function render(DashboardService $d)
    {
        return view('livewire.dashboard.dashboard-kepsek', [
            'guru' => $d->kehadiranPegawai('guru'),
            'tendik' => $d->kehadiranPegawai('tendik'),
            'siswa' => $d->kehadiranSiswa(),
            'kbm' => $d->kbm(),
            'tren' => $d->trenKehadiranGuru(),
            'belumAbsen' => $d->guruBelumAbsen(),
            'alfa' => $d->siswaAlfaBerulang(),
            'surat' => $d->surat(),
            'ambangAlfa' => (int) app(\App\Services\PengaturanService::class)->nilai('ambang_alfa_siswa', 3),
        ]);
    }
}
