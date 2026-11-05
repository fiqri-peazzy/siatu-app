<?php

namespace App\Exports;

use App\Enums\KategoriAbsensi;
use App\Exports\Sheets\DetailKehadiranSheet;
use App\Exports\Sheets\RekapKehadiranSheet;
use App\Services\RekapKehadiranService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Rekap kehadiran bulanan (guru / tendik):
 *   Sheet 1 "Rekap Bulanan"  — matriks pegawai × tanggal, hari off abu-abu
 *   Sheet 2 "Detail Harian"  — jam datang/pulang, status & keterangan per hari
 */
class RekapKehadiranBulananExport implements WithMultipleSheets
{
    private array $data;

    public function __construct(KategoriAbsensi $kategori, Carbon $bulan)
    {
        $this->data = app(RekapKehadiranService::class)->bulanan($kategori, $bulan);
    }

    public function sheets(): array
    {
        return [new RekapKehadiranSheet($this->data), new DetailKehadiranSheet($this->data)];
    }
}
