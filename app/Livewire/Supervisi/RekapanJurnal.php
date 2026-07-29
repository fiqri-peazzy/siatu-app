<?php

namespace App\Livewire\Supervisi;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\JurnalGuru;
use App\Models\Master\MasterGuru;
use Carbon\Carbon;

class RekapanJurnal extends Component
{
    use WithPagination;

    public $search = '';
    public $filterGuru = '';
    public $filterBulan = '';
    public $filterTahun = '';
    public $filterStatus = '';

    public $detailJurnal = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterGuru' => ['except' => ''],
        'filterBulan' => ['except' => ''],
        'filterTahun' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterGuru() { $this->resetPage(); }
    public function updatingFilterBulan() { $this->resetPage(); }
    public function updatingFilterTahun() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }

    public function mount()
    {
        if (!$this->filterBulan) {
            $this->filterBulan = date('m');
        }
        if (!$this->filterTahun) {
            $this->filterTahun = date('Y');
        }
    }

    public function showDetail($idsJson)
    {
        $ids = json_decode($idsJson, true) ?? [$idsJson];
        $jurnals = JurnalGuru::with(['guru', 'kelas', 'mapel'])->whereIn('id', $ids)->orderBy('jam_ke')->get();
        
        if ($jurnals->count() > 0) {
            $this->detailJurnal = clone $jurnals->first();
            $this->detailJurnal->jam_mulai = $jurnals->first()->jam_ke;
            $this->detailJurnal->jam_selesai = $jurnals->last()->jam_ke;
            $this->detailJurnal->total_jp = $jurnals->count();
            
            // Merge dokumentasi if any
            $allDokumentasi = [];
            foreach($jurnals as $j) {
                if ($j->dokumentasi) {
                    $allDokumentasi = array_merge($allDokumentasi, $j->dokumentasi);
                }
            }
            $this->detailJurnal->dokumentasi = array_unique($allDokumentasi);
        }
        
        $this->dispatch('open-modal', 'modal-detail-jurnal');
    }

    public function closeDetail()
    {
        $this->detailJurnal = null;
        $this->dispatch('close-modal', 'modal-detail-jurnal');
    }

    public function render()
    {
        $query = JurnalGuru::with(['guru', 'kelas', 'mapel'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_ke', 'asc');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('materi', 'like', '%' . $this->search . '%')
                  ->orWhere('kegiatan', 'like', '%' . $this->search . '%')
                  ->orWhereHas('guru', function($q) {
                      $q->where('nama', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('mapel', function($q) {
                      $q->where('nama', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('kelas', function($q) {
                      $q->where('nama', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->filterGuru) {
            $query->where('guru_id', $this->filterGuru);
        }

        if ($this->filterBulan) {
            $query->whereMonth('tanggal', $this->filterBulan);
        }

        if ($this->filterTahun) {
            $query->whereYear('tanggal', $this->filterTahun);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $allJurnals = $query->get();

        $grouped = collect();
        $lastGroup = null;

        foreach ($allJurnals as $j) {
            if ($lastGroup && 
                $lastGroup->guru_id == $j->guru_id && 
                $lastGroup->kelas_id == $j->kelas_id && 
                $lastGroup->mapel_id == $j->mapel_id && 
                $lastGroup->tanggal->isSameDay($j->tanggal) && 
                $lastGroup->jam_selesai + 1 == $j->jam_ke) {
                
                $lastGroup->jam_selesai = $j->jam_ke;
                $lastGroup->total_jp++;
                
                // Merge IDs for modal detail if needed, or just use the first id
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

        // Manual Pagination
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 15;
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $grouped->forPage($page, $perPage),
            $grouped->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('livewire.supervisi.rekapan-jurnal', [
            'jurnals' => $paginated,
            'gurus' => MasterGuru::orderBy('nama')->get(),
        ])->layout('components.layouts.app');
    }
}
