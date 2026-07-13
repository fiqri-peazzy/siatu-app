<?php

namespace App\Livewire\Master\Mapel;

use App\Models\Master\MasterMapel;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Support\Str;

#[Layout('components.layouts.app')]
#[Title('Data Mata Pelajaran')]
class ListMapel extends Component
{
    use WithPagination;

    public string $search = '';
    
    // Form properties
    public bool $isModalOpen = false;
    public ?int $editingId = null;
    public string $nama = '';
    public string $kelompok = '';
    public int $jam = 2;
    public string $kurikulum = 'Merdeka';

    protected function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'kelompok' => 'nullable|string|max:255',
            'jam' => 'required|integer|min:1',
            'kurikulum' => 'nullable|string|max:100',
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
    public function mapels()
    {
        return MasterMapel::query()
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('kode', 'like', '%' . $this->search . '%')
                      ->orWhere('kelompok', 'like', '%' . $this->search . '%');
            })
            ->orderBy('kelompok', 'asc')
            ->orderBy('nama', 'asc')
            ->paginate(15);
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['editingId', 'nama', 'kelompok', 'jam', 'kurikulum']);
        $this->jam = 2;
        $this->kurikulum = 'Merdeka';
        $this->isModalOpen = true;
    }

    public function edit(MasterMapel $mapel)
    {
        $this->resetValidation();
        $this->editingId = $mapel->id;
        $this->nama = $mapel->nama;
        $this->kelompok = $mapel->kelompok ?? '';
        $this->jam = $mapel->jam;
        $this->kurikulum = $mapel->kurikulum ?? '';
        
        $this->isModalOpen = true;
    }

    public function store()
    {
        $validatedData = $this->validate();
        
        if (!$this->editingId) {
            // Auto generate kode if creating
            $latest = MasterMapel::latest('id')->first();
            $nextId = $latest ? $latest->id + 1 : 1;
            $validatedData['kode'] = 'MPL-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }

        foreach ($validatedData as $key => $value) {
            if ($value === '') {
                $validatedData[$key] = null;
            }
        }

        MasterMapel::updateOrCreate(
            ['id' => $this->editingId],
            $validatedData
        );

        $this->isModalOpen = false;
        
        session()->flash('success', $this->editingId ? 'Data mata pelajaran berhasil diperbarui.' : 'Data mata pelajaran berhasil ditambahkan.');
    }

    public function delete(MasterMapel $mapel)
    {
        // Check if mapel is used by Guru
        if ($mapel->guru()->exists() ?? false) { // Assuming guru relation is not strictly defined in Mapel model yet, but we can prevent hard errors. 
            // Better check via Guru model
        }
        $isUsed = \App\Models\Master\MasterGuru::where('mapel_id', $mapel->id)->exists();
        if ($isUsed) {
            session()->flash('error', 'Gagal menghapus! Mata pelajaran ini sedang digunakan oleh data Guru.');
            return;
        }

        $mapel->delete();
        session()->flash('success', 'Data mata pelajaran berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.master.mapel.list-mapel');
    }
}
