<?php

namespace App\Livewire\Master\Siswa;

use App\Models\Master\MasterSiswa;
use App\Models\Master\MasterKelas;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

#[Layout('components.layouts.app')]
#[Title('Data Siswa')]
class ListSiswa extends Component
{
    use WithPagination;

    public string $search = '';
    public $filterKelas = '';
    
    // Form properties
    public bool $isModalOpen = false;
    public ?int $editingId = null;
    public string $nis = '';
    public string $nisn = '';
    public string $nama = '';
    public ?int $kelas_id = null;
    public string $status = 'aktif';

    protected function rules()
    {
        return [
            'nis' => 'nullable|string|max:255|unique:master_siswas,nis,' . $this->editingId,
            'nisn' => 'nullable|string|max:255|unique:master_siswas,nisn,' . $this->editingId,
            'nama' => 'required|string|max:255',
            'kelas_id' => 'nullable|exists:master_kelas,id',
            'status' => 'required|in:aktif,lulus,pindah,keluar',
        ];
    }

    public function mount()
    {
        if (!auth()->user()->hasAnyRole(['super-admin', 'admin-tu'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingFilterKelas()
    {
        $this->resetPage();
    }

    #[Computed]
    public function siswas()
    {
        return MasterSiswa::query()
            ->with(['kelas.tahunAjaran'])
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('nis', 'like', '%' . $this->search . '%')
                      ->orWhere('nisn', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterKelas, function ($query) {
                $query->where('kelas_id', $this->filterKelas);
            })
            ->orderBy('nama', 'asc')
            ->paginate(15);
    }
    
    #[Computed]
    public function kelas()
    {
        // Hanya tampilkan kelas dari tahun ajaran aktif
        return MasterKelas::with('tahunAjaran')
            ->whereHas('tahunAjaran', function($q) {
                $q->where('is_active', true);
            })
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get();
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['editingId', 'nis', 'nisn', 'nama', 'kelas_id', 'status']);
        $this->isModalOpen = true;
    }

    public function edit(MasterSiswa $siswa)
    {
        $this->resetValidation();
        $this->editingId = $siswa->id;
        $this->nis = $siswa->nis ?? '';
        $this->nisn = $siswa->nisn ?? '';
        $this->nama = $siswa->nama;
        $this->kelas_id = $siswa->kelas_id;
        $this->status = $siswa->status;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $validatedData = $this->validate();
        
        $validatedData['nis'] = $validatedData['nis'] ?: null;
        $validatedData['nisn'] = $validatedData['nisn'] ?: null;
        $validatedData['kelas_id'] = $validatedData['kelas_id'] ?: null;

        MasterSiswa::updateOrCreate(
            ['id' => $this->editingId],
            $validatedData
        );

        $this->isModalOpen = false;
        
        session()->flash('success', $this->editingId ? 'Data siswa berhasil diperbarui.' : 'Data siswa berhasil ditambahkan.');
    }

    public function delete(MasterSiswa $siswa)
    {
        $siswa->delete();
        session()->flash('success', 'Data siswa berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.master.siswa.list-siswa');
    }
}
