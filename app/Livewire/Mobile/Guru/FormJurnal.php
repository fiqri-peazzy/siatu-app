<?php

namespace App\Livewire\Mobile\Guru;

use Livewire\Component;
use App\Models\Master\MasterJadwalPelajaran;
use App\Models\JurnalGuru;
use App\Models\AbsensiSiswa;
use App\Models\Master\MasterTahunAjaran;
use App\Models\Master\MasterSiswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

class FormJurnal extends Component
{
    use WithFileUploads;

    public $jadwalList;
    public $jadwal; // For displaying the first class's info
    public $kd_cp;
    public $materi;
    public $kegiatan;
    public $dokumentasi = [];
    
    public $siswas = [];
    public $absensi = []; // Array to store attendance status keyed by siswa_id

    public function mount($jadwal_ids)
    {
        $ids = explode(',', $jadwal_ids);
        $this->jadwalList = MasterJadwalPelajaran::whereIn('id', $ids)->get();

        if ($this->jadwalList->isEmpty()) {
            abort(404);
        }

        $this->jadwal = $this->jadwalList->first();
        $guruId = Auth::user()->guru_id ?? Auth::user()->guru->id ?? null;

        foreach ($this->jadwalList as $j) {
            if ($j->guru_id != $guruId) {
                abort(403);
            }
        }

        $this->loadSiswas();
    }

    private function loadSiswas()
    {
        $kelasIds = $this->jadwalList->pluck('kelas_id')->unique();
        $mapelId = $this->jadwal->mapel_id;
        $ta = MasterTahunAjaran::where('is_active', true)->first();

        if (!$ta) {
            $this->siswas = [];
            return;
        }

        // Check if this mapel has any peminatan assignments in this TA
        $isPeminatan = DB::table('peminatan_siswas')
            ->where('mapel_id', $mapelId)
            ->where('tahun_ajaran_id', $ta->id)
            ->exists();

        if ($isPeminatan) {
            $assignedSiswaIds = DB::table('peminatan_siswas')
                ->where('mapel_id', $mapelId)
                ->where('tahun_ajaran_id', $ta->id)
                ->pluck('siswa_id');

            $this->siswas = MasterSiswa::whereIn('kelas_id', $kelasIds)
                ->whereIn('id', $assignedSiswaIds)
                ->where('status', 'aktif')
                ->orderBy('nama', 'asc')
                ->get();
        } else {
            $this->siswas = MasterSiswa::whereIn('kelas_id', $kelasIds)
                ->where('status', 'aktif')
                ->orderBy('nama', 'asc')
                ->get();
        }

        foreach ($this->siswas as $siswa) {
            $this->absensi[$siswa->id] = 'hadir'; // Default
        }
    }

    public function setSemuaAbsensi($status)
    {
        foreach ($this->siswas as $siswa) {
            $this->absensi[$siswa->id] = $status;
        }
    }

    public function submit()
    {
        $this->validate([
            'kd_cp' => 'nullable|string',
            'materi' => 'required|min:5',
            'kegiatan' => 'required|min:5',
            'dokumentasi.*' => 'image|max:5120',
        ]);

        $guruId = Auth::user()->guru_id ?? Auth::user()->guru->id ?? null;
        
        $dokumentasiPaths = [];
        if (!empty($this->dokumentasi)) {
            try {
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                foreach ($this->dokumentasi as $foto) {
                    $image = $manager->decodePath($foto->getRealPath());
                    $image->scaleDown(width: 800, height: 800);
                    $encoded = $image->encodeUsingFileExtension('jpg', quality: 60);
                    
                    $filename = uniqid() . '.jpg';
                    $path = 'jurnal/' . $filename;
                    
                    \Illuminate\Support\Facades\Storage::disk('r2')->put($path, (string) $encoded);
                    $dokumentasiPaths[] = $path;
                }
            } catch (\Exception $e) {
                session()->flash('error', 'Gagal mengunggah dokumentasi: ' . $e->getMessage());
                return;
            }
        }
        
        DB::beginTransaction();
        try {
            $tanggal = Carbon::today()->format('Y-m-d');
            $userId = Auth::id();

            // Calculate overall stats for all students fetched
            $stats = [
                'hadir' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alfa' => 0,
            ];

            foreach ($this->absensi as $siswaId => $status) {
                if (isset($stats[$status])) {
                    $stats[$status]++;
                }
            }

            foreach ($this->jadwalList as $j) {
                // Filter students for this specific class to record individual attendance
                $siswasInKelas = $this->siswas->where('kelas_id', $j->kelas_id);
                
                // Calculate stats for this specific class/jurnal entry
                $kelasStats = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0];

                foreach ($siswasInKelas as $siswa) {
                    $status = $this->absensi[$siswa->id] ?? 'hadir';
                    if (isset($kelasStats[$status])) {
                        $kelasStats[$status]++;
                    }

                    // Save AbsensiSiswa
                    AbsensiSiswa::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'kelas_id' => $j->kelas_id,
                            'tanggal' => $tanggal,
                            'jam_ke' => $j->jam_ke,
                        ],
                        [
                            'status' => $status,
                            'dicatat_oleh' => $userId,
                        ]
                    );
                }

                JurnalGuru::updateOrCreate(
                    [
                        'guru_id' => $guruId,
                        'kelas_id' => $j->kelas_id,
                        'mapel_id' => $j->mapel_id,
                        'tanggal' => $tanggal,
                        'jam_ke' => $j->jam_ke,
                    ],
                    [
                        'kd_cp' => $this->kd_cp,
                        'materi' => $this->materi,
                        'kegiatan' => $this->kegiatan,
                        'dokumentasi' => $dokumentasiPaths,
                        'status' => 'diisi',
                        'jumlah_hadir' => $kelasStats['hadir'],
                        'jumlah_izin' => $kelasStats['izin'],
                        'jumlah_sakit' => $kelasStats['sakit'],
                        'jumlah_alfa' => $kelasStats['alfa'],
                    ]
                );
            }

            DB::commit();

            $this->dispatch('jurnalSubmitted');
            session()->flash('success', 'Jurnal dan Absensi berhasil disimpan!');
            return redirect()->route('guru.dashboard');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.mobile.guru.form-jurnal')->layout('components.layouts.pwa');
    }
}
