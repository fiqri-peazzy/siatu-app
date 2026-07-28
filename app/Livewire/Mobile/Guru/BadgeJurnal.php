<?php

namespace App\Livewire\Mobile\Guru;

use Livewire\Component;
use App\Models\Master\MasterJadwalPelajaran;
use App\Models\JurnalGuru;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BadgeJurnal extends Component
{
    public $unfilledCount = 0;

    protected $listeners = ['jurnalSubmitted' => '$refresh'];

    public function render()
    {
        $guruId = Auth::user()->guru_id ?? Auth::user()->guru->id ?? null;
        if ($guruId) {
            $hariIniStr = Carbon::now()->locale('id')->isoFormat('dddd');
            $tanggalSekarang = Carbon::today()->format('Y-m-d');

            $totalJadwal = MasterJadwalPelajaran::where('guru_id', $guruId)
                ->where('hari', $hariIniStr)
                ->count();

            $totalTerisi = JurnalGuru::where('guru_id', $guruId)
                ->where('tanggal', $tanggalSekarang)
                ->where('status', 'diisi')
                ->count();

            $this->unfilledCount = max(0, $totalJadwal - $totalTerisi);
        }

        return view('livewire.mobile.guru.badge-jurnal');
    }
}
