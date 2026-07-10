<?php

namespace App\Livewire\Administrasi\SuratKeluar;

use App\Models\Master\MasterKategoriSurat;
use App\Models\SuratKeluar;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class ListSuratKeluar extends Component
{
    use WithPagination, AuthorizesRequests;

    public string $search = '';
    public string $filterKategori = '';
    public string $filterStatus = '';
    public string $filterBulan = '';
    public string $filterTahun = '';

    public function mount()
    {
        $this->authorize('viewAny', SuratKeluar::class);
        $this->filterTahun = date('Y');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterKategori()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterBulan()
    {
        $this->resetPage();
    }

    public function updatingFilterTahun()
    {
        $this->resetPage();
    }

    #[Computed]
    public function suratKeluars()
    {
        return SuratKeluar::query()
            ->with(['kategori'])
            ->when($this->search, fn ($q) => $q->where('perihal', 'like', '%' . $this->search . '%')
                                               ->orWhere('no_surat', 'like', '%' . $this->search . '%')
                                               ->orWhere('tujuan', 'like', '%' . $this->search . '%'))
            ->when($this->filterKategori, fn ($q) => $q->where('kategori_surat_id', $this->filterKategori))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterBulan, fn ($q) => $q->whereMonth('tanggal', $this->filterBulan))
            ->when($this->filterTahun, fn ($q) => $q->whereYear('tanggal', $this->filterTahun))
            ->latest('created_at')
            ->paginate(15);
    }

    #[Computed]
    public function kategoris()
    {
        return MasterKategoriSurat::whereIn('tipe', ['keluar', 'keduanya'])->get();
    }

    public function render()
    {
        return view('livewire.administrasi.surat-keluar.list-surat-keluar');
    }
}
