<?php

namespace App\Livewire\Master\Siswa;

use App\Models\Master\MasterSiswa;
use App\Models\Master\MasterKelas;
use App\Imports\SiswaImport;
use App\Exports\SiswaTemplateExport;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.app')]
#[Title('Data Siswa')]
class ListSiswa extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public $filterKelas = '';
    
    public bool $isImportModalOpen = false;
    public $importFile;

    public function mount()
    {
        if (!auth()->user()->hasAnyRole(['super-admin', 'admin-tu', 'admin'])) {
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
        return MasterKelas::with('tahunAjaran')
            ->whereHas('tahunAjaran', function($q) {
                $q->where('is_active', true);
            })
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get();
    }

    public function delete(MasterSiswa $siswa)
    {
        $siswa->delete();
        session()->flash('success', 'Data siswa berhasil dihapus.');
    }

    public function openImportModal()
    {
        $this->reset('importFile');
        $this->isImportModalOpen = true;
    }

    public function downloadTemplate()
    {
        return Excel::download(new SiswaTemplateExport, 'template_import_siswa.xlsx');
    }

    public function import()
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            Excel::import(new SiswaImport, $this->importFile);
            $this->isImportModalOpen = false;
            session()->flash('success', 'Data siswa berhasil diimport.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat import data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.master.siswa.list-siswa');
    }
}

