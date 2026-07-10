<?php

namespace App\Livewire\Administrasi\SuratMasuk;

use App\Models\SuratMasuk;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class DetailSuratMasuk extends Component
{
    use AuthorizesRequests;

    public SuratMasuk $surat;

    public function mount(SuratMasuk $surat)
    {
        $this->authorize('view', $surat);
        $this->surat = $surat->load(['kategori', 'klasifikasiArsip', 'inputOleh', 'disposisis.dariUser', 'disposisis.keUser']);
    }

    public function render()
    {
        return view('livewire.administrasi.surat-masuk.detail-surat-masuk');
    }
}
