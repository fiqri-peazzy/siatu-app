<?php

namespace App\Livewire\Absensi;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Master\MasterGuru;
use App\Models\AbsensiGuru;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapAbsensiExport;

#[Layout('components.layouts.app')]
class DashboardAbsensiHariIni extends Component
{
    use WithPagination;

    public $filterStatus = '';
    public $search = '';
    
    public $startDate;
    public $endDate;
    public $isRange = false;

    public function mount()
    {
        $this->startDate = Carbon::today()->format('Y-m-d');
        $this->endDate = Carbon::today()->format('Y-m-d');
        $this->isRange = false;
    }

    public function updatedStartDate()
    {
        $this->checkIsRange();
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->checkIsRange();
        $this->resetPage();
    }
    
    private function checkIsRange()
    {
        if (Carbon::parse($this->startDate)->gt(Carbon::parse($this->endDate))) {
            $this->endDate = $this->startDate;
        }
        $this->isRange = $this->startDate !== $this->endDate;
    }

    public function exportExcel()
    {
        $fileName = 'Rekap_Absensi_' . $this->startDate . '_sampai_' . $this->endDate . '.xlsx';
        return Excel::download(new RekapAbsensiExport($this->startDate, $this->endDate, $this->filterStatus, $this->search), $fileName);
    }

    public function getGurusProperty()
    {
        $start = $this->startDate;
        $end = $this->endDate;
        
        $query = MasterGuru::query()
            ->with(['absensis' => function($q) use ($start, $end) {
                $q->whereBetween('tanggal', [$start, $end]);
            }])
            ->when($this->search, function($q) {
                $q->where('nama', 'like', "%{$this->search}%");
            });

        if ($this->filterStatus) {
            if ($this->filterStatus === 'belum_absen') {
                $query->whereDoesntHave('absensis', function($q) use ($start, $end) {
                    $q->whereBetween('tanggal', [$start, $end]);
                });
            } else {
                $query->whereHas('absensis', function($q) use ($start, $end) {
                    $q->whereBetween('tanggal', [$start, $end])
                      ->where('jenis', \App\Enums\JenisAbsenGuru::MASUK->value)
                      ->where('status', $this->filterStatus);
                });
            }
        }

        return $query->paginate(15);
    }

    public function render()
    {
        $start = $this->startDate;
        $end = $this->endDate;

        $baseStatQuery = AbsensiGuru::whereBetween('tanggal', [$start, $end])->where('jenis', 'masuk');

        return view('livewire.absensi.dashboard-absensi-hari-ini', [
            'gurus' => $this->gurus,
            'statHadir' => (clone $baseStatQuery)->where('status', 'hadir')->count(),
            'statTelat' => (clone $baseStatQuery)->where('status', 'terlambat')->count(),
            'statIzin' => (clone $baseStatQuery)->whereIn('status', ['izin', 'sakit'])->count(),
            'totalGuru' => MasterGuru::count(),
        ]);
    }
}
