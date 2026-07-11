<?php

namespace App\Livewire\Absensi\Guru;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\AbsensiService;
use App\Models\AbsensiGuru;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.pwa')]
class FormAbsensi extends Component
{
    use WithFileUploads;

    public $latitude;
    public $longitude;
    public $foto;
    public $keterangan = '';
    
    // Status Absen Hari Ini
    public $absenMasuk = null;
    public $absenPulang = null;
    public $jarak = null;
    public $pesanErrorLokasi = null;

    public function mount()
    {
        $guruId = auth()->user()->guru_id;
        $hariIni = Carbon::today()->format('Y-m-d');
        
        $this->absenMasuk = AbsensiGuru::where('guru_id', $guruId)
            ->where('tanggal', $hariIni)
            ->where('jenis', \App\Enums\JenisAbsenGuru::MASUK->value)
            ->first();
            
        $this->absenPulang = AbsensiGuru::where('guru_id', $guruId)
            ->where('tanggal', $hariIni)
            ->where('jenis', \App\Enums\JenisAbsenGuru::PULANG->value)
            ->first();
    }

    public function submitAbsen(string $jenis, AbsensiService $service)
    {
        $this->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|image|max:5120', // Max 5MB
        ]);

        // 1. Validasi Lokasi
        $validasi = $service->validasiLokasi((float) $this->latitude, (float) $this->longitude);
        $this->jarak = $validasi['jarak'];
        
        if (!$validasi['valid']) {
            $this->pesanErrorLokasi = "Anda berada di luar radius sekolah. Jarak Anda: {$this->jarak} meter (Maks: {$validasi['radius_maks']} meter).";
            $this->dispatch('swal:error', $this->pesanErrorLokasi);
            return;
        }

        $waktuAbsen = Carbon::now();
        $status = \App\Enums\StatusAbsensiGuru::HADIR->value;
        
        if ($jenis === \App\Enums\JenisAbsenGuru::MASUK->value && $service->isTerlambat($waktuAbsen)) {
            $status = \App\Enums\StatusAbsensiGuru::TERLAMBAT->value;
        }

        try {
            DB::beginTransaction();
            
            $service->catatAbsenGuru([
                'guru_id' => auth()->user()->guru_id,
                'tanggal' => $waktuAbsen->format('Y-m-d'),
                'jenis' => $jenis,
                'status' => $status,
                'waktu_absen' => $waktuAbsen,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'jarak_dari_sekolah_meter' => $this->jarak,
                'keterangan' => $this->keterangan,
            ], $this->foto);

            DB::commit();
            
            $this->dispatch('swal:success', 'Absensi berhasil dicatat.');
            $this->reset(['foto', 'keterangan', 'latitude', 'longitude']);
            $this->mount();
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal:error', 'Gagal mencatat absen: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.absensi.guru.form-absensi')->layout('components.layouts.pwa');
    }
}
