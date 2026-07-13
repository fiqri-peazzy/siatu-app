<?php

namespace App\Livewire\Master\Tendik;

use App\Models\Master\MasterTendik;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use App\Imports\TendikImport;
use App\Exports\TendikTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.app')]
#[Title('Data Tenaga Kependidikan')]
class ListTendik extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    
    // Import properties
    public bool $isImportModalOpen = false;
    public $importFile;

    // Form properties
    public bool $isModalOpen = false;
    public ?int $editingId = null;
    public string $nip_nik = '';
    public string $nama = '';
    public string $jenis_kelamin = '';
    public string $tempat_lahir = '';
    public string $tanggal_lahir = '';
    public string $no_hp = '';
    public string $email = '';
    public string $alamat = '';
    public string $jabatan = '';
    public string $unit_kerja = '';
    public string $status_kepegawaian = '';
    public string $tmt_tugas = '';
    public string $foto_path = '';
    public string $status = 'aktif';
    public ?int $user_id = null;
    public string $status_akun = 'belum_ada';

    protected function rules()
    {
        return [
            'nip_nik' => 'required|string|max:30|unique:master_tenaga_kependidikans,nip_nik,' . $this->editingId,
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|string|max:1',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
            'jabatan' => 'required|string|max:30',
            'unit_kerja' => 'nullable|string|max:255',
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
    public function tendiks()
    {
        return MasterTendik::query()
            ->with(['user'])
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('nip_nik', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nama', 'asc')
            ->paginate(15);
    }

    #[Computed]
    public function availableUsers()
    {
        // Hanya ambil user yang memiliki role 'staf-tu' atau lainnya yang relevan 
        return User::role('staf-tu')
            ->whereDoesntHave('masterTendik', function($q) {
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
            'editingId', 'nip_nik', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
            'no_hp', 'email', 'alamat', 'jabatan', 'unit_kerja', 
            'status_kepegawaian', 'tmt_tugas', 'foto_path', 'status', 'user_id', 'status_akun'
        ]);
        $this->status = \App\Enums\StatusPersonil::AKTIF->value;
        $this->status_akun = \App\Enums\StatusAkun::BELUM_ADA->value;
        $this->isModalOpen = true;
    }

    public function edit(MasterTendik $tendik)
    {
        $this->resetValidation();
        $this->editingId = $tendik->id;
        $this->nip_nik = $tendik->nip_nik;
        $this->nama = $tendik->nama;
        $this->jenis_kelamin = $tendik->jenis_kelamin ?? '';
        $this->tempat_lahir = $tendik->tempat_lahir ?? '';
        $this->tanggal_lahir = $tendik->tanggal_lahir?->format('Y-m-d') ?? '';
        $this->no_hp = $tendik->no_hp ?? '';
        $this->email = $tendik->email ?? '';
        $this->alamat = $tendik->alamat ?? '';
        $this->jabatan = $tendik->jabatan?->value ?? '';
        $this->unit_kerja = $tendik->unit_kerja ?? '';
        $this->status_kepegawaian = $tendik->status_kepegawaian?->value ?? '';
        $this->tmt_tugas = $tendik->tmt_tugas?->format('Y-m-d') ?? '';
        $this->foto_path = $tendik->foto_path ?? '';
        $this->status = $tendik->status?->value ?? \App\Enums\StatusPersonil::AKTIF->value;
        $this->user_id = $tendik->user_id;
        $this->status_akun = $tendik->status_akun?->value ?? \App\Enums\StatusAkun::BELUM_ADA->value;
        
        $this->isModalOpen = true;
    }

    public function store()
    {
        $validatedData = $this->validate();
        
        foreach ($validatedData as $key => $value) {
            if ($value === '') {
                $validatedData[$key] = null;
            }
        }

        MasterTendik::updateOrCreate(
            ['id' => $this->editingId],
            $validatedData
        );

        $this->isModalOpen = false;
        
        session()->flash('success', $this->editingId ? 'Data tendik berhasil diperbarui.' : 'Data tendik berhasil ditambahkan.');
    }

    public function delete(MasterTendik $tendik)
    {
        $tendik->delete();
        session()->flash('success', 'Data tendik berhasil dihapus.');
    }

    public function openImportModal()
    {
        $this->reset('importFile');
        $this->isImportModalOpen = true;
    }

    public function downloadTemplate()
    {
        return Excel::download(new TendikTemplateExport, 'template_import_tendik.xlsx');
    }

    public function import()
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new TendikImport, $this->importFile);
            $this->isImportModalOpen = false;
            session()->flash('success', 'Data tendik berhasil diimport.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat import data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.master.tendik.list-tendik');
    }
}
