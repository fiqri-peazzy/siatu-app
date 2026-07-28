<?php

namespace App\Livewire\Mobile\Guru;

use Livewire\Component;
use App\Models\Master\MasterJadwalPelajaran;
use App\Models\JurnalGuru;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class FormJurnal extends Component
{
    use WithFileUploads;

    public $jadwalList;
    public $jadwal; // For displaying the first class's info
    public $materi;
    public $kegiatan;
    public $dokumentasi = [];

    public function mount($jadwal_ids)
    {
        $ids = explode(',', $jadwal_ids);
        $this->jadwalList = MasterJadwalPelajaran::whereIn('id', $ids)->get();

        if ($this->jadwalList->isEmpty()) {
            abort(404);
        }

        $this->jadwal = $this->jadwalList->first();
        $guruId = Auth::user()->guru_id ?? Auth::user()->guru->id ?? null;

        foreach ($this->jadwalList as $j) {
            if ($j->guru_id != $guruId) {
                abort(403);
            }
        }
    }

    public function submit()
    {
        $this->validate([
            'materi' => 'required|min:5',
            'kegiatan' => 'required|min:5',
            'dokumentasi.*' => 'image|max:5120', // max 5MB per image
        ]);

        $guruId = Auth::user()->guru_id ?? Auth::user()->guru->id ?? null;
        
        $dokumentasiPaths = [];
        if (!empty($this->dokumentasi)) {
            foreach ($this->dokumentasi as $foto) {
                $dokumentasiPaths[] = $foto->store('jurnal', 'public');
            }
        }
        
        foreach ($this->jadwalList as $j) {
            JurnalGuru::updateOrCreate(
                [
                    'guru_id' => $guruId,
                    'kelas_id' => $j->kelas_id,
                    'mapel_id' => $j->mapel_id,
                    'tanggal' => Carbon::today()->format('Y-m-d'),
                    'jam_ke' => $j->jam_ke,
                ],
                [
                    'materi' => $this->materi,
                    'kegiatan' => $this->kegiatan,
                    'dokumentasi' => $dokumentasiPaths,
                    'status' => 'diisi',
                ]
            );
        }

        $this->dispatch('jurnalSubmitted');
        $this->dispatch('swal:success', 'Jurnal berhasil disimpan!');
        
        return redirect()->route('guru.dashboard');
    }

    public function render()
    {
        return view('livewire.mobile.guru.form-jurnal')->layout('components.layouts.pwa');
    }
}
