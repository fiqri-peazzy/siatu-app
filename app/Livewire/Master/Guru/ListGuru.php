<?php

namespace App\Livewire\Master\Guru;

use App\Models\Master\MasterGuru;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

#[Layout('components.layouts.app')]
#[Title('Data Guru')]
class ListGuru extends Component
{
    use WithPagination;

    public string $search = '';
    
    // Form properties
    public bool $isModalOpen = false;
    public ?int $editingId = null;
    public string $nip = '';
    public string $nama = '';
    public string $jabatan = '';
    public string $mapel_utama = '';
    public string $status = 'aktif';
    public ?int $user_id = null;

    protected function rules()
    {
        return [
            'nip' => 'nullable|string|max:255|unique:master_gurus,nip,' . $this->editingId,
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'mapel_utama' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
            'user_id' => 'nullable|exists:users,id',
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

    #[Computed]
    public function gurus()
    {
        return MasterGuru::query()
            ->with('user')
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('nip', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nama', 'asc')
            ->paginate(15);
    }
    
    #[Computed]
    public function availableUsers()
    {
        // Hanya ambil user yang memiliki role 'guru' dan belum di-assign ke guru lain, atau yang sedang di-edit
        return User::role('guru')
            ->whereDoesntHave('guru', function($q) {
                if ($this->editingId) {
                    $q->where('id', '!=', $this->editingId);
                }
            })
            ->orderBy('name')
            ->get();
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['editingId', 'nip', 'nama', 'jabatan', 'mapel_utama', 'status', 'user_id']);
        $this->isModalOpen = true;
    }

    public function edit(MasterGuru $guru)
    {
        $this->resetValidation();
        $this->editingId = $guru->id;
        $this->nip = $guru->nip ?? '';
        $this->nama = $guru->nama;
        $this->jabatan = $guru->jabatan ?? '';
        $this->mapel_utama = $guru->mapel_utama ?? '';
        $this->status = $guru->status;
        $this->user_id = $guru->user_id;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $validatedData = $this->validate();
        
        // Nullify empty strings
        $validatedData['nip'] = $validatedData['nip'] ?: null;
        $validatedData['jabatan'] = $validatedData['jabatan'] ?: null;
        $validatedData['mapel_utama'] = $validatedData['mapel_utama'] ?: null;
        $validatedData['user_id'] = $validatedData['user_id'] ?: null;

        MasterGuru::updateOrCreate(
            ['id' => $this->editingId],
            $validatedData
        );

        $this->isModalOpen = false;
        
        session()->flash('success', $this->editingId ? 'Data guru berhasil diperbarui.' : 'Data guru berhasil ditambahkan.');
    }

    public function delete(MasterGuru $guru)
    {
        $guru->delete();
        session()->flash('success', 'Data guru berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.master.guru.list-guru');
    }
}
