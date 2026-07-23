<?php

namespace App\Livewire\Master\Kelas;

use App\Models\Master\MasterKelas;
use App\Models\Master\MasterGuru;
use App\Models\Master\MasterTahunAjaran;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

#[Layout('components.layouts.app')]
#[Title('Data Kelas')]
class ListKelas extends Component
{
    use WithPagination;

    public string $search = '';
    public $filterTahunAjaran = '';
    
    // Form properties
    public bool $isModalOpen = false;
    public ?int $editingId = null;
    public string $nama = '';
    public string $tingkat = 'X';
    public string $jurusan = '';
    public ?int $wali_kelas_id = null;
    public ?int $tahun_ajaran_id = null;

    protected function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'tingkat' => 'required|in:X,XI,XII',
            'jurusan' => 'nullable|string|max:255',
            'wali_kelas_id' => 'nullable|exists:master_gurus,id',
            'tahun_ajaran_id' => 'required|exists:master_tahun_ajarans,id',
        ];
    }

    public function mount()
    {
        if (!auth()->user()->hasAnyRole(['super-admin', 'admin-tu'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Default filter ke tahun ajaran aktif jika ada
        $activeTa = MasterTahunAjaran::where('is_active', true)->first();
        if ($activeTa) {
            $this->filterTahunAjaran = $activeTa->id;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingFilterTahunAjaran()
    {
        $this->resetPage();
    }

    #[Computed]
    public function kelas()
    {
        return MasterKelas::query()
            ->with(['waliKelas', 'tahunAjaran'])
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterTahunAjaran, function ($query) {
                $query->where('tahun_ajaran_id', $this->filterTahunAjaran);
            })
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->paginate(15);
    }
    
    #[Computed]
    public function tahunAjarans()
    {
        return MasterTahunAjaran::orderBy('mulai', 'desc')->get();
    }
    
    #[Computed]
    public function gurus()
    {
        return MasterGuru::where('status', 'aktif')->orderBy('nama')->get();
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['editingId', 'nama', 'tingkat', 'jurusan', 'wali_kelas_id']);
        
        $activeTa = MasterTahunAjaran::where('is_active', true)->first();
        $this->tahun_ajaran_id = $activeTa ? $activeTa->id : null;

        $this->isModalOpen = true;
    }

    public function edit(MasterKelas $kelas)
    {
        $this->resetValidation();
        $this->editingId = $kelas->id;
        $this->nama = $kelas->nama;
        $this->tingkat = $kelas->tingkat;
        $this->jurusan = $kelas->jurusan;
        $this->wali_kelas_id = $kelas->wali_kelas_id;
        $this->tahun_ajaran_id = $kelas->tahun_ajaran_id;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $validatedData = $this->validate();
        
        $validatedData['wali_kelas_id'] = $validatedData['wali_kelas_id'] ?: null;

        MasterKelas::updateOrCreate(
            ['id' => $this->editingId],
            $validatedData
        );

        $this->isModalOpen = false;
        
        session()->flash('success', $this->editingId ? 'Data kelas berhasil diperbarui.' : 'Data kelas berhasil ditambahkan.');
    }

    public function delete(MasterKelas $kelas)
    {
        $kelas->delete();
        session()->flash('success', 'Data kelas berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.master.kelas.list-kelas');
    }
}
