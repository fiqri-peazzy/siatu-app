<?php

namespace App\Livewire\Absensi\Guru;

use Livewire\Component;
use App\Models\AbsensiGuru;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.pwa')]
class RiwayatAbsensi extends Component
{
    public $filterBulan;
    public $filterTahun;

    public function mount()
    {
        $this->filterBulan = date('m');
        $this->filterTahun = date('Y');
    }

    public function getRiwayatProperty()
    {
        $records = AbsensiGuru::where('guru_id', auth()->user()->guru_id)
            ->whereMonth('tanggal', $this->filterBulan)
            ->whereYear('tanggal', $this->filterTahun)
            ->orderBy('tanggal', 'desc')
            ->get();
            
        $grouped = [];
        foreach ($records as $record) {
            if (!isset($grouped[$record->tanggal])) {
                $grouped[$record->tanggal] = [
                    'tanggal' => $record->tanggal,
                    'masuk' => null, 
                    'pulang' => null
                ];
            }
            $grouped[$record->tanggal][$record->jenis] = $record;
        }
        
        return $grouped;
    }

    public function render()
    {
        return view('livewire.absensi.guru.riwayat-absensi', [
            'riwayat' => $this->riwayat
        ])->layout('components.layouts.pwa');
    }
}
