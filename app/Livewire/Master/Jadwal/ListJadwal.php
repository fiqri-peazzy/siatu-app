<?php

namespace App\Livewire\Master\Jadwal;

use App\Models\Master\MasterJadwalPelajaran;
use App\Models\Master\MasterTahunAjaran;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterMapel;
use App\Models\Master\MasterGuru;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Illuminate\Validation\ValidationException;

#[Layout('components.layouts.app')]
#[Title('Jadwal Pelajaran')]
class ListJadwal extends Component
{
    // Filter
    public ?int $filterTahunAjaran = null;
    public ?int $filterKelas = null;

    // Form Properties
    public bool $isModalOpen = false;
    public ?int $editingId = null;
    public ?int $form_tahun_ajaran_id = null;
    public ?int $form_kelas_id = null;
    public ?int $form_mapel_id = null;
    public ?int $form_guru_id = null;
    public string $form_hari = '';
    public string $form_jam_mulai = '';
    public string $form_jam_selesai = '';
    public ?int $form_jam_ke = null;

    public $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

    public function mount()
    {
        if (!auth()->user()->hasAnyRole(['super-admin', 'admin-tu', 'kurikulum'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $activeTahunAjaran = MasterTahunAjaran::where('is_active', true)->first();
        if ($activeTahunAjaran) {
            $this->filterTahunAjaran = $activeTahunAjaran->id;
            $this->form_tahun_ajaran_id = $activeTahunAjaran->id;
        }

        $firstKelas = MasterKelas::orderBy('tingkat')->orderBy('nama')->first();
        if ($firstKelas) {
            $this->filterKelas = $firstKelas->id;
            $this->form_kelas_id = $firstKelas->id;
        }
    }

    #[Computed]
    public function tahunAjarans()
    {
        return MasterTahunAjaran::orderBy('id', 'desc')->get();
    }

    #[Computed]
    public function kelasList()
    {
        return MasterKelas::with('tahunAjaran')->orderBy('tingkat')->orderBy('nama')->get();
    }

    #[Computed]
    public function mapels()
    {
        return MasterMapel::orderBy('nama')->get();
    }

    #[Computed]
    public function gurus()
    {
        return MasterGuru::orderBy('nama')->get();
    }

    #[Computed]
    public function jadwals()
    {
        if (!$this->filterTahunAjaran || !$this->filterKelas) {
            return collect();
        }

        // Return jadwals grouped by hari
        $jadwals = MasterJadwalPelajaran::with(['mapel', 'guru'])
            ->where('tahun_ajaran_id', $this->filterTahunAjaran)
            ->where('kelas_id', $this->filterKelas)
            ->orderBy('jam_mulai')
            ->get();

        $grouped = [];
        foreach ($this->hariList as $hari) {
            $grouped[$hari] = collect();
        }
        
        foreach ($jadwals as $jadwal) {
            if (isset($grouped[$jadwal->hari])) {
                $grouped[$jadwal->hari]->push($jadwal);
            }
        }
        
        return $grouped;
    }

    protected function rules()
    {
        return [
            'form_tahun_ajaran_id' => 'required|exists:master_tahun_ajarans,id',
            'form_kelas_id' => 'required|exists:master_kelas,id',
            'form_mapel_id' => 'required|exists:master_mapels,id',
            'form_guru_id' => 'required|exists:master_gurus,id',
            'form_hari' => 'required|string',
            'form_jam_mulai' => 'required',
            'form_jam_selesai' => 'required|after:form_jam_mulai',
            'form_jam_ke' => 'required|integer|min:1',
        ];
    }

    public function create($hari = null, $jamKe = null)
    {
        $this->resetValidation();
        $this->reset(['editingId', 'form_mapel_id', 'form_guru_id', 'form_jam_mulai', 'form_jam_selesai', 'form_jam_ke']);
        
        $this->form_tahun_ajaran_id = $this->filterTahunAjaran;
        $this->form_kelas_id = $this->filterKelas;
        if ($hari) {
            $this->form_hari = $hari;
        } else {
            $this->form_hari = 'Senin';
        }
        
        if ($jamKe) {
            $this->form_jam_ke = $jamKe;
        }
        
        $this->isModalOpen = true;
    }

    public function edit(MasterJadwalPelajaran $jadwal)
    {
        $this->resetValidation();
        $this->editingId = $jadwal->id;
        $this->form_tahun_ajaran_id = $jadwal->tahun_ajaran_id;
        $this->form_kelas_id = $jadwal->kelas_id;
        $this->form_mapel_id = $jadwal->mapel_id;
        $this->form_guru_id = $jadwal->guru_id;
        $this->form_hari = $jadwal->hari;
        // format to H:i
        $this->form_jam_mulai = substr($jadwal->jam_mulai, 0, 5);
        $this->form_jam_selesai = substr($jadwal->jam_selesai, 0, 5);
        $this->form_jam_ke = $jadwal->jam_ke;
        
        $this->isModalOpen = true;
    }

    public function store()
    {
        $validatedData = $this->validate();

        // Check for conflicts
        $query = MasterJadwalPelajaran::where('tahun_ajaran_id', $this->form_tahun_ajaran_id)
            ->where('hari', $this->form_hari)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('jam_mulai', '<', $this->form_jam_selesai)
                       ->where('jam_selesai', '>', $this->form_jam_mulai);
                });
            });

        if ($this->editingId) {
            $query->where('id', '!=', $this->editingId);
        }

        $conflicts = $query->get();

        foreach ($conflicts as $conflict) {
            // Rule 1: Same teacher cannot teach in different classes at the same time
            if ($conflict->guru_id == $this->form_guru_id) {
                throw ValidationException::withMessages([
                    'form_jam_mulai' => 'Guru ini sudah memiliki jadwal mengajar di kelas lain pada waktu tersebut.',
                ]);
            }

            // Rule 2: Same class cannot have different subjects at the same time
            if ($conflict->kelas_id == $this->form_kelas_id) {
                throw ValidationException::withMessages([
                    'form_jam_mulai' => 'Kelas ini sudah memiliki jadwal pelajaran lain pada waktu tersebut.',
                ]);
            }
        }

        MasterJadwalPelajaran::updateOrCreate(
            ['id' => $this->editingId],
            [
                'tahun_ajaran_id' => $this->form_tahun_ajaran_id,
                'kelas_id' => $this->form_kelas_id,
                'mapel_id' => $this->form_mapel_id,
                'guru_id' => $this->form_guru_id,
                'hari' => $this->form_hari,
                'jam_mulai' => $this->form_jam_mulai,
                'jam_selesai' => $this->form_jam_selesai,
                'jam_ke' => $this->form_jam_ke,
            ]
        );

        $this->isModalOpen = false;
        session()->flash('success', $this->editingId ? 'Jadwal berhasil diperbarui.' : 'Jadwal berhasil ditambahkan.');
    }

    public function delete(MasterJadwalPelajaran $jadwal)
    {
        $jadwal->delete();
        session()->flash('success', 'Jadwal berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.master.jadwal.list-jadwal');
    }
}
