<?php

namespace App\Livewire\Mobile\Guru;

use Livewire\Component;
use App\Models\Master\MasterJadwalPelajaran;
use App\Models\JurnalGuru;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FormJurnal extends Component
{
    public $jadwal;
    public $materi;
    public $kegiatan;

    public function mount(MasterJadwalPelajaran $jadwal)
    {
        $this->jadwal = $jadwal;
        $guruId = Auth::user()->guru_id ?? Auth::user()->guru->id ?? null;

        if ($jadwal->guru_id != $guruId) {
            abort(403);
        }
    }

    public function submit()
    {
        $this->validate([
            'materi' => 'required|min:5',
            'kegiatan' => 'required|min:5',
        ]);

        $guruId = Auth::user()->guru_id ?? Auth::user()->guru->id ?? null;
        
        JurnalGuru::updateOrCreate(
            [
                'guru_id' => $guruId,
                'kelas_id' => $this->jadwal->kelas_id,
                'mapel_id' => $this->jadwal->mapel_id,
                'tanggal' => Carbon::today()->format('Y-m-d'),
                'jam_ke' => $this->jadwal->jam_ke,
            ],
            [
                'materi' => $this->materi,
                'kegiatan' => $this->kegiatan,
                'status' => 'diisi',
            ]
        );

        $this->dispatch('jurnalSubmitted');
        $this->dispatch('swal:success', 'Jurnal berhasil disimpan!');
        
        return redirect()->route('guru.dashboard');
    }

    public function render()
    {
        return view('livewire.mobile.guru.form-jurnal')->layout('components.layouts.pwa');
    }
}
