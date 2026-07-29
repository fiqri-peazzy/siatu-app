<?php

namespace App\Livewire\Akademik\Peminatan;

use Livewire\Component;
use App\Models\Master\MasterMapel;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterSiswa;
use App\Models\Master\MasterTahunAjaran;
use Illuminate\Support\Facades\DB;

class AssignPeminatan extends Component
{
    public $tahun_ajaran_id;
    public $mapel_id;
    public $kelas_id;

    public $mapels = [];
    public $kelasList = [];
    public $tahunAjarans = [];
    
    public $siswas = [];
    public $selectedSiswas = []; // Checkboxes

    public function mount()
    {
        $this->tahunAjarans = MasterTahunAjaran::orderBy('mulai', 'desc')->get();
        $this->mapels = MasterMapel::orderBy('nama', 'asc')->get();
        $this->kelasList = MasterKelas::orderBy('nama', 'asc')->get();

        $activeTa = $this->tahunAjarans->firstWhere('is_active', true);
        if ($activeTa) {
            $this->tahun_ajaran_id = $activeTa->id;
        }
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['tahun_ajaran_id', 'mapel_id', 'kelas_id'])) {
            $this->loadSiswas();
        }
    }

    public function loadSiswas()
    {
        if ($this->kelas_id && $this->mapel_id && $this->tahun_ajaran_id) {
            $this->siswas = MasterSiswa::where('kelas_id', $this->kelas_id)
                ->where('status', 'aktif')
                ->orderBy('nama', 'asc')
                ->get();

            // Fetch already assigned siswas for this mapel & tahun ajaran
            $this->selectedSiswas = DB::table('peminatan_siswas')
                ->where('mapel_id', $this->mapel_id)
                ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
                ->whereIn('siswa_id', $this->siswas->pluck('id')->toArray())
                ->pluck('siswa_id')
                ->toArray();
        } else {
            $this->siswas = [];
            $this->selectedSiswas = [];
        }
    }

    public function selectAll()
    {
        $this->selectedSiswas = $this->siswas->pluck('id')->toArray();
    }

    public function deselectAll()
    {
        $this->selectedSiswas = [];
    }

    public function save()
    {
        $this->validate([
            'tahun_ajaran_id' => 'required',
            'mapel_id' => 'required',
            'kelas_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // Get all siswas in the selected class
            $siswaIdsInClass = $this->siswas->pluck('id')->toArray();

            // Remove existing assignments for this class, mapel, and tahun ajaran
            DB::table('peminatan_siswas')
                ->where('mapel_id', $this->mapel_id)
                ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
                ->whereIn('siswa_id', $siswaIdsInClass)
                ->delete();

            // Insert new assignments
            $dataToInsert = [];
            foreach ($this->selectedSiswas as $siswaId) {
                $dataToInsert[] = [
                    'siswa_id' => $siswaId,
                    'mapel_id' => $this->mapel_id,
                    'tahun_ajaran_id' => $this->tahun_ajaran_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (count($dataToInsert) > 0) {
                DB::table('peminatan_siswas')->insert($dataToInsert);
            }

            DB::commit();
            session()->flash('success', 'Data peminatan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.akademik.peminatan.assign-peminatan')
            ->layout('layouts.app'); // Ensure this is the correct layout
    }
}
