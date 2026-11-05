<?php

namespace App\Livewire\Absensi\Tendik;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Enums\JenisAbsenGuru;
use App\Enums\KategoriAbsensi;
use App\Services\AbsensiService;
use App\Services\AturanAbsensiService;
use App\Models\AbsensiTendik;
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
    public $dalamRadius = false;

    /** Hasil cek jendela waktu saat halaman dibuka (AturanAbsensiService::evaluasi) */
    public array $cekWaktu = [];

    /** Ringkasan jam absen hari ini untuk garis waktu (AturanAbsensiService::jendela) */
    public array $jendela = [];

    /** Jenis absen berikutnya: masuk / pulang */
    public string $jenisAbsen = 'masuk';

    public function mount(AturanAbsensiService $aturan)
    {
        $tendikId = auth()->user()->masterTendik->id ?? null;
        
        if (!$tendikId) {
            session()->flash('error', 'Akun Anda belum terhubung dengan data Tenaga Kependidikan. Silakan hubungi Super Admin.');
            return redirect()->route('dashboard');
        }

        $hariIni = Carbon::today()->format('Y-m-d');
        
        $this->absenMasuk = AbsensiTendik::where('tendik_id', $tendikId)
            ->where('tanggal', $hariIni)
            ->where('jenis', \App\Enums\JenisAbsenGuru::MASUK->value)
            ->first();
            
        $this->absenPulang = AbsensiTendik::where('tendik_id', $tendikId)
            ->where('tanggal', $hariIni)
            ->where('jenis', \App\Enums\JenisAbsenGuru::PULANG->value)
            ->first();

        $this->cekWaktu = $this->evaluasiWaktu($aturan, Carbon::now());
        $this->jendela = $aturan->jendela(KategoriAbsensi::TENDIK, Carbon::now());
        $this->jenisAbsen = $this->absenMasuk ? 'pulang' : 'masuk';
    }

    private function evaluasiWaktu(AturanAbsensiService $aturan, Carbon $waktu, ?string $alasan = null): array
    {
        return $aturan->evaluasi(
            KategoriAbsensi::TENDIK,
            $this->absenMasuk ? JenisAbsenGuru::PULANG : JenisAbsenGuru::MASUK,
            $waktu,
            sudahMasuk: (bool) $this->absenMasuk,
            sudahPulang: (bool) $this->absenPulang,
            alasan: $alasan,
        );
    }

    public function cekLokasi(AbsensiService $service)
    {
        if (!$this->latitude || !$this->longitude) {
            return false;
        }

        $validasi = $service->validasiLokasi((float) $this->latitude, (float) $this->longitude);
        $this->jarak = $validasi['jarak'];
        
        if (!$validasi['valid']) {
            $this->dalamRadius = false;
            $this->pesanErrorLokasi = "Anda berada di luar area absen. Jarak Anda: {$this->jarak} meter (Maks: {$validasi['radius_maks']} meter).";
            return false;
        }

        $this->dalamRadius = true;
        $this->pesanErrorLokasi = null;
        return true;
    }

    public function submitAbsen(AbsensiService $service, AturanAbsensiService $aturan)
    {
        $this->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|string', // Base64 string
        ]);

        $jenis = $this->absenMasuk ? \App\Enums\JenisAbsenGuru::PULANG->value : \App\Enums\JenisAbsenGuru::MASUK->value;

        // 1. Validasi Lokasi
        $validasi = $service->validasiLokasi((float) $this->latitude, (float) $this->longitude);
        $this->jarak = $validasi['jarak'];
        
        if (!$validasi['valid']) {
            $this->pesanErrorLokasi = "Anda berada di luar radius sekolah. Jarak Anda: {$this->jarak} meter (Maks: {$validasi['radius_maks']} meter).";
            $this->dispatch('swal:error', $this->pesanErrorLokasi);
            return;
        }

        // 2. Validasi jendela waktu (dicek ulang di server saat submit)
        $waktuAbsen = Carbon::now();
        $hasil = $this->evaluasiWaktu($aturan, $waktuAbsen, $this->keterangan);

        if (! $hasil['boleh']) {
            $this->cekWaktu = $hasil;
            $this->dispatch('swal:error', $hasil['pesan']);
            return;
        }

        $status = $hasil['status'];

        try {
            DB::beginTransaction();
            
            $service->catatAbsenTendik([
                'tendik_id' => auth()->user()->masterTendik->id,
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
            
            return redirect()->route('akademik.absensi.tendik.riwayat');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal:error', 'Gagal mencatat absen: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.absensi.tendik.form-absensi')->layout('components.layouts.pwa');
    }
}
