<?php

namespace App\Livewire\Akademik\Absensi;

use Livewire\Component;
use App\Models\Master\MasterTahunAjaran;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterMapel;
use App\Models\Master\MasterSiswa;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekapSiswa extends Component
{
    public $tahunAjarans = [];
    public $kelasList = [];
    public $mapels = [];

    public $tahun_ajaran_id;
    public $kelas_id;
    public $mapel_id = 'semua';
    public $tipe_waktu = 'bulan'; // hari, bulan, semester
    
    public $tanggal_harian;
    public $bulan_bulanan;
    
    public $rekapData = [];
    public $kolomTanggal = []; // Untuk mapel spesifik
    
    public function mount()
    {
        $this->tahunAjarans = MasterTahunAjaran::orderBy('mulai', 'desc')->get();
        $this->kelasList = MasterKelas::orderBy('nama', 'asc')->get();
        $this->mapels = MasterMapel::orderBy('nama', 'asc')->get();

        $activeTa = $this->tahunAjarans->firstWhere('is_active', true);
        if ($activeTa) {
            $this->tahun_ajaran_id = $activeTa->id;
        }

        $this->tanggal_harian = date('Y-m-d');
        $this->bulan_bulanan = date('Y-m');
    }

    public function updated($propertyName)
    {
        $this->generateRekap();
    }

    public function generateRekap()
    {
        if (!$this->kelas_id || !$this->tahun_ajaran_id) {
            $this->rekapData = [];
            $this->kolomTanggal = [];
            return;
        }

        // Tentukan rentang tanggal
        $startDate = null;
        $endDate = null;

        if ($this->tipe_waktu == 'hari') {
            $startDate = $this->tanggal_harian;
            $endDate = $this->tanggal_harian;
        } elseif ($this->tipe_waktu == 'bulan') {
            if ($this->bulan_bulanan) {
                $startDate = $this->bulan_bulanan . '-01';
                $endDate = date('Y-m-t', strtotime($startDate));
            }
        } elseif ($this->tipe_waktu == 'semester') {
            $ta = $this->tahunAjarans->firstWhere('id', $this->tahun_ajaran_id);
            if ($ta) {
                // Asumsi semester ganjil dimulai Juli, genap dimulai Januari
                if (strtolower($ta->semester) == 'ganjil' || $ta->semester == 1) {
                    $startDate = date('Y', strtotime($ta->mulai)) . '-07-01';
                    $endDate = date('Y', strtotime($ta->mulai)) . '-12-31';
                } else {
                    $startDate = date('Y', strtotime($ta->selesai)) . '-01-01';
                    $endDate = date('Y', strtotime($ta->selesai)) . '-06-30';
                }
            }
        }

        if (!$startDate || !$endDate) return;

        // Ambil data siswa
        $siswas = MasterSiswa::where('kelas_id', $this->kelas_id)
            ->where('status', 'aktif')
            ->orderBy('nama', 'asc')
            ->get();

        // Ambil data absensi + join jurnal untuk tahu mapel
        $query = DB::table('absensi_siswas')
            ->join('jurnal_gurus', function ($join) {
                $join->on('absensi_siswas.kelas_id', '=', 'jurnal_gurus.kelas_id')
                     ->on('absensi_siswas.tanggal', '=', 'jurnal_gurus.tanggal')
                     ->on('absensi_siswas.jam_ke', '=', 'jurnal_gurus.jam_ke');
            })
            ->where('absensi_siswas.kelas_id', $this->kelas_id)
            ->whereBetween('absensi_siswas.tanggal', [$startDate, $endDate]);

        if ($this->mapel_id != 'semua') {
            $query->where('jurnal_gurus.mapel_id', $this->mapel_id);
        }

        $absensiRaw = $query->select(
            'absensi_siswas.siswa_id',
            'absensi_siswas.status',
            'absensi_siswas.tanggal',
            'absensi_siswas.bukti_file',
            'jurnal_gurus.mapel_id'
        )->get();

        $data = [];
        $tanggals = [];

        foreach ($siswas as $siswa) {
            $data[$siswa->id] = [
                'siswa' => $siswa,
                'stats' => ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alfa' => 0],
                'per_tanggal' => []
            ];
        }

        foreach ($absensiRaw as $ab) {
            if (!isset($data[$ab->siswa_id])) continue;
            
            // Rekap Global
            $status = strtolower($ab->status);
            if (isset($data[$ab->siswa_id]['stats'][$status])) {
                $data[$ab->siswa_id]['stats'][$status]++;
            }

            // Detail per tanggal jika 1 mapel
            if ($this->mapel_id != 'semua') {
                $data[$ab->siswa_id]['per_tanggal'][$ab->tanggal] = [
                    'status' => $status,
                    'bukti_file' => $ab->bukti_file
                ];
                $tanggals[$ab->tanggal] = true;
            }
        }

        if ($this->mapel_id != 'semua') {
            $this->kolomTanggal = array_keys($tanggals);
            sort($this->kolomTanggal);
        } else {
            $this->kolomTanggal = [];
        }

        $this->rekapData = $data;
    }

    public function exportExcel()
    {
        $this->validate([
            'tahun_ajaran_id' => 'required',
            'kelas_id' => 'required',
        ]);

        return redirect()->route('akademik.absensi-siswa.export', [
            'tahun_ajaran_id' => $this->tahun_ajaran_id,
            'kelas_id' => $this->kelas_id,
            'mapel_id' => $this->mapel_id,
            'tipe_waktu' => $this->tipe_waktu,
            'tanggal_harian' => $this->tanggal_harian,
            'bulan_bulanan' => $this->bulan_bulanan,
        ]);
    }

    public function render()
    {
        return view('livewire.akademik.absensi.rekap-siswa')->layout('components.layouts.app');
    }
}
