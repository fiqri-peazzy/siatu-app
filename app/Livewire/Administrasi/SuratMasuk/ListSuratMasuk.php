<?php

namespace App\Livewire\Administrasi\SuratMasuk;

use App\Models\Master\MasterKategoriSurat;
use App\Models\SuratMasuk;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class ListSuratMasuk extends Component
{
    use WithPagination, AuthorizesRequests;

    public string $search = '';
    public string $filterKategori = '';
    public string $filterStatus = '';
    public string $filterBulan = '';
    public string $filterTahun = '';

    public function mount()
    {
        $this->authorize('viewAny', SuratMasuk::class);
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
    public function suratMasuks()
    {
        $query = SuratMasuk::query()
            ->with(['kategori', 'klasifikasiArsip']);

        // Jika login sebagai guru, hanya tampilkan yang didisposisikan kepadanya
        if (auth()->user()->hasRole('guru')) {
            $query->whereHas('disposisis', function ($q) {
                $q->where('ke_user_id', auth()->id());
            });
        }

        return $query
            ->when($this->search, fn ($q) => $q->where('perihal', 'like', '%' . $this->search . '%')
                                               ->orWhere('no_agenda', 'like', '%' . $this->search . '%')
                                               ->orWhere('asal_instansi', 'like', '%' . $this->search . '%'))
            ->when($this->filterKategori, fn ($q) => $q->where('kategori_surat_id', $this->filterKategori))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterBulan, fn ($q) => $q->whereMonth('tanggal_terima', $this->filterBulan))
            ->when($this->filterTahun, fn ($q) => $q->whereYear('tanggal_terima', $this->filterTahun))
            ->latest('tanggal_terima')
            ->paginate(15);
    }

    #[Computed]
    public function kategoris()
    {
        return MasterKategoriSurat::whereIn('tipe', ['masuk', 'keduanya'])->get();
    }

    public function render()
    {
        return view('livewire.administrasi.surat-masuk.list-surat-masuk');
    }
}
