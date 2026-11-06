<?php

namespace App\Exports\Sheets;

use App\Exports\Concerns\KopResmi;
use App\Services\LaporanBulananService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/** Matriks kehadiran siswa satu rombel × tanggal; hari bukan sekolah abu-abu. Ditandatangani wali kelas. */
class MatriksAbsensiSiswaSheet implements FromArray, WithEvents, WithTitle
{
    use KopResmi;

    private const TOTAL = ['H' => 'Hadir', 'I' => 'Izin', 'S' => 'Sakit', 'A' => 'Alfa'];

    public function __construct(private array $data, private array $kelas) {}

    public function title(): string
    {
        // Nama sheet Excel maks. 31 karakter & tanpa karakter khusus
        return mb_substr(preg_replace('/[\\\\\/\?\*\[\]:]/', '-', $this->kelas['nama']), 0, 31);
    }

    public function array(): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [AfterSheet::class => fn (AfterSheet $e) => $this->tulis($e->sheet->getDelegate())];
    }

    private function tulis(Worksheet $sheet): void
    {
        $hari = array_values($this->data['hari']);
        $nHari = count($hari);
        $col = fn (int $i) => Coordinate::stringFromColumnIndex($i);
        $kolomTotal = 3 + $nHari + 1;
        $kolomAkhir = $kolomTotal + count(self::TOTAL) + 1; // + jam alfa + % hadir

        $bulan = $this->data['bulan']->locale('id')->isoFormat('MMMM YYYY');
        $r = $this->pasangKop($sheet, $kolomAkhir, 'Daftar Hadir Siswa Kelas '.$this->kelas['nama'],
            "Bulan {$bulan} · Wali kelas: ".($this->kelas['wali'] ?? '-'));

        $sheet->setCellValue("A{$r}", 'No')->setCellValue("B{$r}", 'Nama Siswa')->setCellValue("C{$r}", 'NIS');
        foreach (['A', 'B', 'C'] as $c) {
            $sheet->mergeCells("{$c}{$r}:{$c}".($r + 1));
        }
        foreach ($hari as $i => $h) {
            $c = $col(4 + $i);
            $sheet->setCellValue("{$c}{$r}", $h['tgl']->day);
            $sheet->setCellValue($c.($r + 1), mb_substr($h['tgl']->locale('id')->isoFormat('dd'), 0, 2));
            $sheet->getColumnDimension($c)->setWidth(3.6);
        }
        $c = $kolomTotal;
        foreach ([...array_values(self::TOTAL), 'Jam Alfa', '% Hadir'] as $judul) {
            $sheet->setCellValue($col($c).$r, $judul)->mergeCells($col($c).$r.':'.$col($c).($r + 1));
            $sheet->getColumnDimension($col($c))->setWidth(7);
            $c++;
        }
        $this->gayaJudulTabel($sheet, "A{$r}:".$col($kolomAkhir).($r + 1));
        $sheet->getStyle($col(4).$r.':'.$col(3 + $nHari).($r + 1))->getFill()->getStartColor()->setARGB('FF16345E');
        $sheet->freezePane($col(4).($r + 2));
        $barisJudul = $r;
        $r += 2;

        foreach ($this->kelas['siswa'] as $n => $s) {
            $sheet->setCellValue("A{$r}", $n + 1)->setCellValue("B{$r}", $s['nama']);
            $sheet->setCellValueExplicit("C{$r}", (string) $s['nis'], DataType::TYPE_STRING);
            foreach ($hari as $i => $h) {
                $kode = $s['sel'][$h['tgl']->day] ?? '';
                $sel = $col(4 + $i).$r;
                $sheet->setCellValue($sel, $kode === 'L' ? '' : $kode);
                $warna = $kode !== '' ? LaporanBulananService::KODE_SISWA[$kode][1] : ($h['off'] ? LaporanBulananService::KODE_SISWA['L'][1] : null);
                if ($warna) {
                    $sheet->getStyle($sel)->getFill()->setFillType('solid')->getStartColor()->setARGB($warna);
                }
            }
            $c = $kolomTotal;
            foreach (array_keys(self::TOTAL) as $k) {
                $sheet->setCellValue($col($c++).$r, $s['total'][$k]);
            }
            $sheet->setCellValue($col($c++).$r, $s['jam_alfa']);
            $sheet->setCellValue($col($c).$r, $s['persen'] === null ? '-' : $s['persen'] / 100);
            $sheet->getStyle($col($c).$r)->getNumberFormat()->setFormatCode('0.0%');
            if ($s['total']['A'] >= $this->data['ambang']) {
                $sheet->getStyle("B{$r}")->getFont()->setBold(true)->getColor()->setARGB('FFB42318');
            }
            $r++;
        }

        $akhirData = max($r - 1, $barisJudul + 2);
        $this->gayaIsiTabel($sheet, 'A'.($barisJudul + 2).':'.$col($kolomAkhir).$akhirData);
        $sheet->getStyle($col(4).($barisJudul + 2).':'.$col($kolomAkhir).$akhirData)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A'.($barisJudul + 2).":A{$akhirData}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getColumnDimension('A')->setWidth(4);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(12);

        $r++;
        $sheet->setCellValue("B{$r}", 'Keterangan:')->getStyle("B{$r}")->getFont()->setBold(true)->setSize(9);
        foreach (LaporanBulananService::KODE_SISWA as $kode => [$arti, $warna]) {
            $r++;
            $sheet->setCellValue("A{$r}", $kode === 'L' ? '' : $kode);
            $sheet->getStyle("A{$r}")->getFill()->setFillType('solid')->getStartColor()->setARGB($warna);
            $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("B{$r}", ($kode === 'L' ? 'Abu-abu' : $kode).' = '.$arti);
            $sheet->getStyle("A{$r}:B{$r}")->getFont()->setSize(9);
        }
        $r++;
        $sheet->setCellValue("B{$r}", "Nama bercetak merah = alfa ≥ {$this->data['ambang']} hari.")->getStyle("B{$r}")->getFont()->setSize(9)->setItalic(true);

        $this->tandaTanganWaliDanKepsek($sheet, $r + 2, $kolomAkhir);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($barisJudul, $barisJudul + 1);
    }

    /** Wali kelas di kiri, kepala sekolah di kanan */
    private function tandaTanganWaliDanKepsek(Worksheet $sheet, int $baris, int $kolomAkhir): void
    {
        $this->pasangTandaTangan($sheet, $baris, $kolomAkhir);
        $isi = ['', 'Wali Kelas,', '', '', '', $this->kelas['wali'] ?? '...........................', ''];
        foreach ($isi as $i => $teks) {
            $r = $baris + $i;
            $sheet->setCellValue("B{$r}", $teks);
            $sheet->getStyle("B{$r}")->applyFromArray([
                'font' => ['size' => 10, 'bold' => $i === 5, 'underline' => $i === 5],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }
    }
}
