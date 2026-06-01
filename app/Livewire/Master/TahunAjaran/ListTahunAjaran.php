<?php

namespace App\Livewire\Master\TahunAjaran;

use App\Models\Master\MasterTahunAjaran;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

#[Layout('components.layouts.app')]
#[Title('Data Tahun Ajaran')]
class ListTahunAjaran extends Component
{
    use WithPagination;

    public string $search = '';
    
    // Form properties
    public bool $isModalOpen = false;
    public ?int $editingId = null;
    public string $nama = '';
    public string $semester = 'Ganjil';
    public string $mulai = '';
    public string $selesai = '';
    public bool $is_active = false;

    protected $rules = [
        'nama' => 'required|string|max:255',
        'semester' => 'required|in:Ganjil,Genap',
        'mulai' => 'required|date',
        'selesai' => 'required|date|after:mulai',
        'is_active' => 'boolean',
    ];

    public function mount()
    {
        // Otorisasi: hanya role yang diizinkan (admin-tu, super-admin, dll)
        if (!auth()->user()->hasAnyRole(['super-admin', 'admin-tu'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function tahunAjarans()
    {
        return MasterTahunAjaran::query()
            ->when($this->search, fn($query) => $query->where('nama', 'like', '%' . $this->search . '%'))
            ->orderBy('mulai', 'desc')
            ->paginate(15);
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['editingId', 'nama', 'semester', 'mulai', 'selesai', 'is_active']);
        $this->isModalOpen = true;
    }

    public function edit(MasterTahunAjaran $tahunAjaran)
    {
        $this->resetValidation();
        $this->editingId = $tahunAjaran->id;
        $this->nama = $tahunAjaran->nama;
        $this->semester = $tahunAjaran->semester;
        $this->mulai = $tahunAjaran->mulai->format('Y-m-d');
        $this->selesai = $tahunAjaran->selesai->format('Y-m-d');
        $this->is_active = $tahunAjaran->is_active;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $validatedData = $this->validate();

        // Jika diset active, nonaktifkan yang lain
        if ($this->is_active) {
            MasterTahunAjaran::query()->update(['is_active' => false]);
        }

        MasterTahunAjaran::updateOrCreate(
            ['id' => $this->editingId],
            $validatedData
        );

        $this->isModalOpen = false;
        
        session()->flash('success', $this->editingId ? 'Tahun ajaran berhasil diperbarui.' : 'Tahun ajaran berhasil ditambahkan.');
    }

    public function delete(MasterTahunAjaran $tahunAjaran)
    {
        $tahunAjaran->delete();
        session()->flash('success', 'Tahun ajaran berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.master.tahun-ajaran.list-tahun-ajaran');
    }
}
