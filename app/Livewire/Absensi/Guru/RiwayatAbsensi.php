<?php

namespace App\Livewire\Absensi\Guru;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AbsensiGuru;
use Carbon\Carbon;

class RiwayatAbsensi extends Component
{
    use WithPagination;

    public $filterBulan;
    public $filterTahun;

    public function mount()
    {
        $this->filterBulan = date('m');
        $this->filterTahun = date('Y');
    }

    public function getRiwayatProperty()
    {
        return AbsensiGuru::where('guru_id', auth()->user()->guru_id)
            ->whereMonth('tanggal', $this->filterBulan)
            ->whereYear('tanggal', $this->filterTahun)
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu_absen', 'desc')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.absensi.guru.riwayat-absensi', [
            'riwayat' => $this->riwayat
        ]);
    }
}
