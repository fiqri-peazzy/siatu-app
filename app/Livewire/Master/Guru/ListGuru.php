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
    public string $nuptk = '';
    public string $nama = '';
    public string $jenis_kelamin = '';
    public string $tempat_lahir = '';
    public string $tanggal_lahir = '';
    public string $no_hp = '';
    public string $email = '';
    public string $alamat = '';
    public string $jabatan = '';
    public ?int $mapel_id = null;
    public string $pendidikan_terakhir = '';
    public string $status_kepegawaian = '';
    public string $tmt_tugas = '';
    public string $foto_path = '';
    public string $status = 'aktif';
    public ?int $user_id = null;
    public string $status_akun = 'belum_ada';

    protected function rules()
    {
        return [
            'nip' => 'nullable|string|max:30|unique:master_gurus,nip,' . $this->editingId,
            'nuptk' => 'nullable|string|max:30',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|string|max:1',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
            'jabatan' => 'nullable|string|max:255',
            'mapel_id' => 'nullable|exists:master_mapels,id',
            'pendidikan_terakhir' => 'nullable|string|max:255',
            'status_kepegawaian' => 'nullable|string|max:20',
            'tmt_tugas' => 'nullable|date',
            'foto_path' => 'nullable|string|max:255',
            'status' => 'required|string|max:20',
            'user_id' => 'nullable|exists:users,id',
            'status_akun' => 'required|string|max:20',
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
            ->with(['user', 'mapel'])
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('nip', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nama', 'asc')
            ->paginate(15);
    }
    
    #[Computed]
    public function mapels()
    {
        return \App\Models\Master\MasterMapel::orderBy('nama', 'asc')->get();
    }

    #[Computed]
    public function availableUsers()
    {
        // Hanya ambil user yang memiliki role 'guru' dan belum di-assign ke guru lain, atau yang sedang di-edit
        return User::role('guru')
            ->whereDoesntHave('masterGuru', function($q) {
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
        $this->reset([
            'editingId', 'nip', 'nuptk', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
            'no_hp', 'email', 'alamat', 'jabatan', 'mapel_id', 'pendidikan_terakhir', 
            'status_kepegawaian', 'tmt_tugas', 'foto_path', 'status', 'user_id', 'status_akun'
        ]);
        $this->status = \App\Enums\StatusPersonil::AKTIF->value;
        $this->status_akun = \App\Enums\StatusAkun::BELUM_ADA->value;
        $this->isModalOpen = true;
    }

    public function edit(MasterGuru $guru)
    {
        $this->resetValidation();
        $this->editingId = $guru->id;
        $this->nip = $guru->nip ?? '';
        $this->nuptk = $guru->nuptk ?? '';
        $this->nama = $guru->nama;
        $this->jenis_kelamin = $guru->jenis_kelamin ?? '';
        $this->tempat_lahir = $guru->tempat_lahir ?? '';
        $this->tanggal_lahir = $guru->tanggal_lahir?->format('Y-m-d') ?? '';
        $this->no_hp = $guru->no_hp ?? '';
        $this->email = $guru->email ?? '';
        $this->alamat = $guru->alamat ?? '';
        $this->jabatan = $guru->jabatan ?? '';
        $this->mapel_id = $guru->mapel_id;
        $this->pendidikan_terakhir = $guru->pendidikan_terakhir ?? '';
        $this->status_kepegawaian = $guru->status_kepegawaian?->value ?? '';
        $this->tmt_tugas = $guru->tmt_tugas?->format('Y-m-d') ?? '';
        $this->foto_path = $guru->foto_path ?? '';
        $this->status = $guru->status?->value ?? \App\Enums\StatusPersonil::AKTIF->value;
        $this->user_id = $guru->user_id;
        $this->status_akun = $guru->status_akun?->value ?? \App\Enums\StatusAkun::BELUM_ADA->value;
        
        $this->isModalOpen = true;
    }

    public function store()
    {
        $validatedData = $this->validate();
        
        // Nullify empty strings
        foreach ($validatedData as $key => $value) {
            if ($value === '') {
                $validatedData[$key] = null;
            }
        }

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
