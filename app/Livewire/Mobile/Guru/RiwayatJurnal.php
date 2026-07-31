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

    public $selectedJurnals = null;
    public $detailMateri = '';
    public $detailCatatan = '';
    public $detailAktivitas = '';
    public $showModal = false;

    public function mount()
    {
        $this->filterBulan = Carbon::now()->format('m');
        $this->filterTahun = Carbon::now()->format('Y');
    }

    public function showDetail($jurnalIds)
    {
        $jurnals = JurnalGuru::with(['kelas', 'mapel'])->whereIn('id', $jurnalIds)->get();
        if ($jurnals->isEmpty()) return;

        $this->selectedJurnals = $jurnals;
        $this->detailMateri = $jurnals->first()->materi;
        $this->detailAktivitas = $jurnals->first()->kegiatan; // Using 'kegiatan' if it exists, otherwise empty
        $this->detailCatatan = $jurnals->first()->catatan;

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedJurnals = null;
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

        $allJurnals = $query->orderBy('tanggal', 'desc')->orderBy('jam_ke', 'asc')->get();

        $grouped = collect();
        $lastGroup = null;

        foreach ($allJurnals as $j) {
            if ($lastGroup && 
                $lastGroup->kelas_id == $j->kelas_id && 
                $lastGroup->mapel_id == $j->mapel_id && 
                $lastGroup->tanggal->isSameDay($j->tanggal) && 
                $lastGroup->jam_selesai + 1 == $j->jam_ke) {
                
                $lastGroup->jam_selesai = $j->jam_ke;
                $lastGroup->total_jp++;
                $ids = $lastGroup->jurnal_ids;
                $ids[] = $j->id;
                $lastGroup->jurnal_ids = $ids;
            } else {
                if ($lastGroup) {
                    $grouped->push($lastGroup);
                }
                
                $lastGroup = clone $j;
                $lastGroup->jam_mulai = $j->jam_ke;
                $lastGroup->jam_selesai = $j->jam_ke;
                $lastGroup->total_jp = 1;
                $lastGroup->jurnal_ids = [$j->id];
            }
        }
        
        if ($lastGroup) {
            $grouped->push($lastGroup);
        }

        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 10;
        $jurnals = new \Illuminate\Pagination\LengthAwarePaginator(
            $grouped->forPage($page, $perPage),
            $grouped->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
        
        $kelasList = MasterKelas::orderBy('nama')->get();
        $mapelList = MasterMapel::orderBy('nama')->get();

        return view('livewire.mobile.guru.riwayat-jurnal', [
            'jurnals' => $jurnals,
            'kelasList' => $kelasList,
            'mapelList' => $mapelList,
        ])->layout('components.layouts.pwa');
    }
}
