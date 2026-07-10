<?php

namespace App\Services;

use App\Models\Master\MasterKategoriSurat;
use App\Models\SuratMasuk;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SuratService
{
    /**
     * Konversi angka bulan ke angka romawi
     */
    private function getRomawiBulan(int $bulan): string
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[$bulan] ?? 'I';
    }

    /**
     * Generate Nomor Agenda untuk Surat Masuk
     * Format: {NO_URUT}/{KODE_KATEGORI}/{ROMAWI_BULAN}/{TAHUN}
     */
    public function generateNomorAgenda(int $kategoriId, Carbon $tanggalTerima): string
    {
        $kategori = MasterKategoriSurat::findOrFail($kategoriId);
        
        $tahun = $tanggalTerima->year;
        $bulan = $tanggalTerima->month;
        $romawiBulan = $this->getRomawiBulan($bulan);

        // Hitung nomor urut berdasarkan surat masuk di kategori dan tahun yang sama
        $count = SuratMasuk::where('kategori_surat_id', $kategoriId)
                           ->whereYear('tanggal_terima', $tahun)
                           ->count();
        
        $noUrut = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        $kodeKategori = $kategori->kode;

        // Format akhir
        $nomorAgenda = "{$noUrut}/{$kodeKategori}/{$romawiBulan}/{$tahun}";

        // Handle possible duplicate (meskipun count() sudah membantu)
        $suffix = 1;
        $originalNomor = $nomorAgenda;
        while (SuratMasuk::where('no_agenda', $nomorAgenda)->exists()) {
            $nomorAgenda = "{$originalNomor}-{$suffix}";
            $suffix++;
        }

        return $nomorAgenda;
    }

    /**
     * Simpan Surat Masuk ke Database
     */
    public function simpanSuratMasuk(array $data, ?UploadedFile $file): SuratMasuk
    {
        return DB::transaction(function () use ($data, $file) {
            $tanggalTerima = Carbon::parse($data['tanggal_terima']);
            
            // Auto-generate nomor agenda
            $data['no_agenda'] = $this->generateNomorAgenda($data['kategori_surat_id'], $tanggalTerima);
            
            // Handle file upload
            if ($file) {
                $tahun = $tanggalTerima->year;
                $bulan = str_pad($tanggalTerima->month, 2, '0', STR_PAD_LEFT);
                $path = $file->store("surat/masuk/{$tahun}/{$bulan}", 'local');
                $data['file_path'] = $path;
            }

            // Simpan ke DB
            return SuratMasuk::create($data);
        });
    }

    /**
     * Update Surat Masuk
     */
    public function updateSuratMasuk(SuratMasuk $surat, array $data, ?UploadedFile $file): SuratMasuk
    {
        return DB::transaction(function () use ($surat, $data, $file) {
            if ($file) {
                $tanggalTerima = Carbon::parse($data['tanggal_terima'] ?? $surat->tanggal_terima);
                $tahun = $tanggalTerima->year;
                $bulan = str_pad($tanggalTerima->month, 2, '0', STR_PAD_LEFT);
                $path = $file->store("surat/masuk/{$tahun}/{$bulan}", 'local');
                $data['file_path'] = $path;
            }

            $surat->update($data);
            return $surat;
        });
    }
}
