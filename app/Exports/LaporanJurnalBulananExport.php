<?php

namespace App\Exports;

use App\Exports\Sheets\DetailJurnalSheet;
use App\Exports\Sheets\RekapJurnalGuruSheet;
use App\Services\LaporanBulananService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Laporan jurnal mengajar bulanan (berkop resmi):
 *   Sheet "Rekap per Guru" — pertemuan wajib, terisi, sebagian, belum, % keterisian
 *   Sheet "Daftar Jurnal"  — tanggal, guru, mapel, rombel, jam, materi, kegiatan, kehadiran
 */
class LaporanJurnalBulananExport implements WithMultipleSheets
{
    private array $data;

    public function __construct(Carbon $bulan)
    {
        $this->data = app(LaporanBulananService::class)->jurnal($bulan);
    }

    public function sheets(): array
    {
        return [new RekapJurnalGuruSheet($this->data), new DetailJurnalSheet($this->data)];
    }
}
