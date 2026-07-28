<?php

namespace App\Livewire\Mobile\Guru;

use App\Models\JurnalGuru;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterMapel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class RiwayatJurnal extends Component
{
    use WithPagination;

    public $filterBulan;
    public $filterTahun;
    public $filterKelas = '';
    public $filterMapel = '';

    public function mount()
    {
        $this->filterBulan = Carbon::now()->format('m');
        $this->filterTahun = Carbon::now()->format('Y');
    }

    public function updatingFilterBulan() { $this->resetPage(); }
    public function updatingFilterTahun() { $this->resetPage(); }
    public function updatingFilterKelas() { $this->resetPage(); }
    public function updatingFilterMapel() { $this->resetPage(); }

    public function render()
    {
        $guruId = Auth::user()->guru_id ?? Auth::user()->guru->id ?? null;
        
        $query = JurnalGuru::with(['kelas', 'mapel'])
            ->where('guru_id', $guruId)
            ->where('status', 'diisi')
            ->whereMonth('tanggal', $this->filterBulan)
            ->whereYear('tanggal', $this->filterTahun);

        if ($this->filterKelas != '') {
            $query->where('kelas_id', $this->filterKelas);
        }
        if ($this->filterMapel != '') {
            $query->where('mapel_id', $this->filterMapel);
        }

        $jurnals = $query->orderBy('tanggal', 'desc')->orderBy('jam_ke', 'asc')->paginate(10);
        
        $kelasList = MasterKelas::orderBy('nama')->get();
        $mapelList = MasterMapel::orderBy('nama')->get();

        return view('livewire.mobile.guru.riwayat-jurnal', [
            'jurnals' => $jurnals,
            'kelasList' => $kelasList,
            'mapelList' => $mapelList,
        ])->layout('components.layouts.pwa');
    }
}
