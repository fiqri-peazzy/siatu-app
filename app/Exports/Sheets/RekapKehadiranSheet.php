<?php

namespace App\Exports\Sheets;

use App\Exports\Concerns\KopResmi;
use App\Services\RekapKehadiranService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/** Matriks kehadiran pegawai × tanggal (1..akhir bulan); hari off berwarna abu-abu. */
class RekapKehadiranSheet implements FromArray, WithEvents, WithTitle
{
    use KopResmi;

    private const TOTAL = ['H' => 'Hadir', 'T' => 'Telat', 'PC' => 'Plg Cpt', 'I' => 'Izin', 'S' => 'Sakit', 'DL' => 'DL', 'A' => 'Alfa'];

    public function __construct(private array $data) {}

    public function title(): string
    {
        return 'Rekap Bulanan';
    }

    public function array(): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [AfterSheet::class => fn (AfterSheet $e) => $this->tulis($e->sheet->getDelegate())];
    }

    private function tulis($sheet): void
    {
        $hari = $this->data['hari'];
        $nHari = count($hari);
        $kolomTotal = 3 + $nHari + 1;                          // No, Nama, NIP, tgl..., lalu total
        $kolomAkhir = $kolomTotal + count(self::TOTAL) + 1;     // + hari efektif + %
        $col = fn (int $i) => Coordinate::stringFromColumnIndex($i);
        $label = $this->data['kategori']->label();

        $r = $this->pasangKop($sheet, $kolomAkhir, "Rekap Kehadiran {$label}",
            'Bulan '.$this->data['bulan']->locale('id')->isoFormat('MMMM YYYY'));

        // Judul kolom (2 baris: tanggal + inisial hari)
        $sheet->setCellValue("A{$r}", 'No')->setCellValue("B{$r}", 'Nama')->setCellValue("C{$r}", 'NIP / NUPTK');
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
        foreach (self::TOTAL as $judul) {
            $sheet->setCellValue($col($c).$r, $judul);
            $sheet->mergeCells($col($c).$r.':'.$col($c).($r + 1));
            $sheet->getColumnDimension($col($c))->setWidth(6.5);
            $c++;
        }
        $sheet->setCellValue($col($c).$r, 'Hari Efektif')->mergeCells($col($c).$r.':'.$col($c).($r + 1));
        $sheet->setCellValue($col($c + 1).$r, '% Hadir')->mergeCells($col($c + 1).$r.':'.$col($c + 1).($r + 1));
        $this->gayaJudulTabel($sheet, "A{$r}:".$col($kolomAkhir).($r + 1));
        $sheet->getStyle($col(4).$r.':'.$col(3 + $nHari).($r + 1))->getFill()->getStartColor()->setARGB('FF16345E');
        $sheet->freezePane($col(4).($r + 2));
        $barisJudul = $r;
        $r += 2;

        // Isi
        foreach ($this->data['pegawai'] as $n => $p) {
            $sheet->setCellValue("A{$r}", $n + 1)->setCellValue("B{$r}", $p['nama']);
            $sheet->setCellValueExplicit("C{$r}", (string) $p['nip'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            foreach ($hari as $i => $h) {
                $kode = $p['sel'][$h['tgl']->day] ?? '';
                $c = $col(4 + $i).$r;
                $sheet->setCellValue($c, $kode === 'L' ? '' : $kode);
                $warna = $kode !== '' ? RekapKehadiranService::KODE[$kode][1] : ($h['off'] ? RekapKehadiranService::KODE['L'][1] : null);
                if ($warna) {
                    $sheet->getStyle($c)->getFill()->setFillType('solid')->getStartColor()->setARGB($warna);
                }
            }
            $c = $kolomTotal;
            foreach (array_keys(self::TOTAL) as $k) {
                $sheet->setCellValue($col($c++).$r, $p['total'][$k]);
            }
            $sheet->setCellValue($col($c).$r, $p['efektif']);
            $sheet->setCellValue($col($c + 1).$r, $p['persen'] / 100);
            $sheet->getStyle($col($c + 1).$r)->getNumberFormat()->setFormatCode('0.0%');
            $r++;
        }

        $akhirData = max($r - 1, $barisJudul + 2);
        $this->gayaIsiTabel($sheet, "A".($barisJudul + 2).':'.$col($kolomAkhir).$akhirData);
        $sheet->getStyle($col(4).($barisJudul + 2).':'.$col($kolomAkhir).$akhirData)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A".($barisJudul + 2).":A{$akhirData}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getColumnDimension('A')->setWidth(4);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension($col($kolomAkhir - 1))->setWidth(8);
        $sheet->getColumnDimension($col($kolomAkhir))->setWidth(8);

        // Keterangan kode
        $r += 1;
        $sheet->setCellValue("B{$r}", 'Keterangan:')->getStyle("B{$r}")->getFont()->setBold(true)->setSize(9);
        foreach (RekapKehadiranService::KODE as $kode => [$arti, $warna]) {
            $r++;
            $sheet->setCellValue("A{$r}", $kode === 'L' ? '' : $kode);
            $sheet->getStyle("A{$r}")->getFill()->setFillType('solid')->getStartColor()->setARGB($warna);
            $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("B{$r}", ($kode === 'L' ? 'Abu-abu' : $kode).' = '.$arti);
            $sheet->getStyle("A{$r}:B{$r}")->getFont()->setSize(9);
        }

        $this->pasangTandaTangan($sheet, $r + 2, $kolomAkhir);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($barisJudul, $barisJudul + 1);
    }
}
