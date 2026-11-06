<?php

namespace App\Exports;

use App\Exports\Sheets\MatriksAbsensiSiswaSheet;
use App\Exports\Sheets\RingkasanAbsensiSiswaSheet;
use App\Services\LaporanBulananService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Laporan absensi siswa bulanan (berkop resmi):
 *   Sheet "Ringkasan"   — per rombel: % hadir, total H/I/S/A, jam alfa, siswa alfa berulang
 *   Sheet per rombel    — matriks siswa × tanggal 1..akhir bulan, hari off abu-abu
 */
class LaporanAbsensiSiswaBulananExport implements WithMultipleSheets
{
    private array $data;

    public function __construct(Carbon $bulan)
    {
        $this->data = app(LaporanBulananService::class)->absensiSiswa($bulan);
    }

    public function sheets(): array
    {
        $sheets = [new RingkasanAbsensiSiswaSheet($this->data)];
        foreach ($this->data['kelas'] as $kelas) {
            $sheets[] = new MatriksAbsensiSiswaSheet($this->data, $kelas);
        }

        return $sheets;
    }
}
