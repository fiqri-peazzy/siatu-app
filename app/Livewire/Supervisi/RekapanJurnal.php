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

    public function showDetail($id)
    {
        $this->detailJurnal = JurnalGuru::with(['guru', 'kelas', 'mapel'])->find($id);
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

        return view('livewire.supervisi.rekapan-jurnal', [
            'jurnals' => $query->paginate(15),
            'gurus' => MasterGuru::orderBy('nama')->get(),
        ])->layout('components.layouts.app');
    }
}
