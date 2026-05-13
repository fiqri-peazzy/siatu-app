<?php

namespace App\Services;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuratService
{
    /**
     * Generate Nomor Agenda Surat Masuk.
     * Format: {KODE_KATEGORI}/{ROMAWI_BULAN}/{TAHUN}
     */
    public function generateNomorAgenda(string $kodeKategori): string
    {
        $bulanRomawi = $this->getRomawi(date('n'));
        $tahun = date('Y');
        
        // Count existing for this category and year
        // Placeholder until models are ready
        return "{$kodeKategori}/{$bulanRomawi}/{$tahun}";
    }

    /**
     * Generate Nomor Surat Keluar.
     * Format: {NO_URUT}/{KODE}/{ROMAWI_BULAN}/{TAHUN}
     */
    public function generateNomorSuratKeluar(string $kode): string
    {
        $bulanRomawi = $this->getRomawi(date('n'));
        $tahun = date('Y');
        
        // Placeholder
        $noUrut = '001'; 
        
        return "{$noUrut}/{$kode}/{$bulanRomawi}/{$tahun}";
    }

    private function getRomawi(int $n): string
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[$n] ?? '';
    }
}
