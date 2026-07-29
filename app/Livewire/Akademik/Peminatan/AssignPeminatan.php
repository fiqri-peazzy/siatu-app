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
    public $viewMode = 'index'; // 'index' or 'manage'
    public $activeTab = 'peserta'; // 'peserta' or 'assign'

    public $tahun_ajaran_id;
    public $mapel_id;
    public $kelas_id;
    public $selectedMapel = null;

    public $mapels = [];
    public $kelasList = [];
    public $tahunAjarans = [];
    
    public $siswas = []; // For assign tab
    public $selectedSiswas = []; // Checkboxes for assign tab
    
    public $pesertaMapel = []; // For peserta tab
    public $mapelWithCounts = []; // For index tab

    public function mount()
    {
        $this->tahunAjarans = MasterTahunAjaran::orderBy('mulai', 'desc')->get();
        $this->kelasList = MasterKelas::orderBy('nama', 'asc')->get();

        $activeTa = $this->tahunAjarans->firstWhere('is_active', true);
        if ($activeTa) {
            $this->tahun_ajaran_id = $activeTa->id;
        }
        
        $this->loadMapelWithCounts();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'tahun_ajaran_id') {
            if ($this->viewMode === 'index') {
                $this->loadMapelWithCounts();
            } else {
                $this->loadPeserta();
                $this->loadSiswas();
            }
        }
        
        if (in_array($propertyName, ['kelas_id', 'activeTab'])) {
            if ($this->activeTab == 'assign') {
                $this->loadSiswas();
            } else {
                $this->loadPeserta();
            }
        }
    }

    public function loadMapelWithCounts()
    {
        if ($this->tahun_ajaran_id) {
            $this->mapelWithCounts = MasterMapel::withCount(['peminatanSiswas' => function($query) {
                $query->where('peminatan_siswas.tahun_ajaran_id', $this->tahun_ajaran_id);
            }])->orderBy('nama', 'asc')->get();
        } else {
            $this->mapelWithCounts = [];
        }
    }

    public function manageMapel($id)
    {
        $this->mapel_id = $id;
        $this->selectedMapel = MasterMapel::find($id);
        $this->viewMode = 'manage';
        $this->activeTab = 'peserta';
        $this->loadPeserta();
    }

    public function backToIndex()
    {
        $this->viewMode = 'index';
        $this->mapel_id = null;
        $this->selectedMapel = null;
        $this->loadMapelWithCounts();
    }

    public function loadPeserta()
    {
        if ($this->mapel_id && $this->tahun_ajaran_id) {
            $this->pesertaMapel = MasterSiswa::whereHas('peminatanMapels', function($q) {
                    $q->where('mapel_id', $this->mapel_id)
                      ->where('tahun_ajaran_id', $this->tahun_ajaran_id);
                })
                ->with('kelas')
                ->orderBy('kelas_id')
                ->orderBy('nama')
                ->get();
        } else {
            $this->pesertaMapel = [];
        }
    }

    public function removePeserta($siswaId)
    {
        DB::table('peminatan_siswas')
            ->where('mapel_id', $this->mapel_id)
            ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->where('siswa_id', $siswaId)
            ->delete();
            
        $this->loadPeserta();
        session()->flash('success', 'Siswa berhasil dihapus dari peminatan ini.');
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
            ->layout('components.layouts.app'); // Changed to use component layout properly if needed, but 'layouts.app' was originally there. Wait, I will use components.layouts.app
    }
}
