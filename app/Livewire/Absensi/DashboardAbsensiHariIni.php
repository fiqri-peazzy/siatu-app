<?php

namespace App\Livewire\Absensi;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Master\MasterGuru;
use App\Models\AbsensiGuru;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class DashboardAbsensiHariIni extends Component
{
    use WithPagination;

    public $filterStatus = '';
    public $search = '';

    // Listen to the event for realtime updates (optional if using broadcasting, but here we can just poll or manual refresh)
    // #[On('echo:absensi,AbsensiGuruDicatat')] // If broadcasting is setup

    public function getGurusProperty()
    {
        $hariIni = Carbon::today()->format('Y-m-d');
        
        $query = MasterGuru::query()
            ->with(['absensis' => function($q) use ($hariIni) {
                $q->where('tanggal', $hariIni)
                  ->where('jenis', \App\Enums\JenisAbsenGuru::MASUK->value);
            }])
            ->when($this->search, function($q) {
                $q->where('nama', 'like', "%{$this->search}%");
            });

        if ($this->filterStatus) {
            if ($this->filterStatus === 'belum_absen') {
                $query->whereDoesntHave('absensis', function($q) use ($hariIni) {
                    $q->where('tanggal', $hariIni)
                      ->where('jenis', \App\Enums\JenisAbsenGuru::MASUK->value);
                });
            } else {
                $query->whereHas('absensis', function($q) use ($hariIni) {
                    $q->where('tanggal', $hariIni)
                      ->where('jenis', \App\Enums\JenisAbsenGuru::MASUK->value)
                      ->where('status', $this->filterStatus);
                });
            }
        }

        return $query->paginate(15);
    }

    public function render()
    {
        return view('livewire.absensi.dashboard-absensi-hari-ini', [
            'gurus' => $this->gurus,
            'statHadir' => AbsensiGuru::where('tanggal', Carbon::today()->format('Y-m-d'))->where('jenis', 'masuk')->where('status', 'hadir')->count(),
            'statTelat' => AbsensiGuru::where('tanggal', Carbon::today()->format('Y-m-d'))->where('jenis', 'masuk')->where('status', 'terlambat')->count(),
            'statIzin' => AbsensiGuru::where('tanggal', Carbon::today()->format('Y-m-d'))->where('jenis', 'masuk')->whereIn('status', ['izin', 'sakit'])->count(),
            'totalGuru' => MasterGuru::count(),
        ]);
    }
}
