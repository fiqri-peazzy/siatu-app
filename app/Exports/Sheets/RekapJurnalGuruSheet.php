<?php

namespace App\Exports\Sheets;

use App\Exports\Concerns\KopResmi;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/** Keterisian jurnal mengajar per guru dalam satu bulan. */
class RekapJurnalGuruSheet implements FromArray, WithEvents, WithTitle
{
    use KopResmi;

    private const JUDUL = ['No', 'Nama Guru', 'NIP', 'Pertemuan Wajib', 'Terisi', 'Sebagian', 'Belum Diisi', '% Keterisian'];

    public function __construct(private array $data) {}

    public function title(): string
    {
        return 'Rekap per Guru';
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
        $n = count(self::JUDUL);
        $bulan = $this->data['bulan']->locale('id')->isoFormat('MMMM YYYY');
        $sampai = $this->data['sampai']->locale('id')->isoFormat('D MMMM YYYY');
        $r = $this->pasangKop($sheet, $n, 'Rekap Keterisian Jurnal Mengajar', "Bulan {$bulan} · data sampai {$sampai}");

        $sheet->fromArray(self::JUDUL, null, "A{$r}");
        $this->gayaJudulTabel($sheet, "A{$r}:H{$r}");
        $awal = ++$r;

        foreach ($this->data['guru'] as $i => $g) {
            $sheet->fromArray([$i + 1, $g['nama'], null, $g['wajib'], $g['diisi'], $g['sebagian'], $g['belum'],
                $g['persen'] === null ? '-' : $g['persen'] / 100], null, "A{$r}", true);
            $sheet->setCellValueExplicit("C{$r}", (string) ($g['nip'] ?: '-'), DataType::TYPE_STRING);
            if ($g['persen'] !== null) {
                $sheet->getStyle("H{$r}")->getNumberFormat()->setFormatCode('0.0%');
                $warna = $g['persen'] >= 90 ? 'FFE7F3EC' : ($g['persen'] >= 70 ? 'FFFDF1DD' : 'FFFBE1DE');
                $sheet->getStyle("H{$r}")->getFill()->setFillType('solid')->getStartColor()->setARGB($warna);
            }
            $r++;
        }

        // Baris total
        $t = $this->data['total'];
        $sheet->setCellValue("B{$r}", 'JUMLAH')->setCellValue("D{$r}", $t['wajib'])->setCellValue("E{$r}", $t['diisi'])
            ->setCellValue("F{$r}", array_sum(array_column($this->data['guru'], 'sebagian')))
            ->setCellValue("G{$r}", array_sum(array_column($this->data['guru'], 'belum')))
            ->setCellValue("H{$r}", $t['persen'] === null ? '-' : $t['persen'] / 100);
        $sheet->getStyle("H{$r}")->getNumberFormat()->setFormatCode('0.0%');
        $sheet->getStyle("A{$r}:H{$r}")->getFont()->setBold(true);

        $this->gayaIsiTabel($sheet, "A{$awal}:H{$r}");
        $sheet->getStyle("D{$awal}:H{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$awal}:A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        foreach (['A' => 4, 'B' => 32, 'C' => 22, 'D' => 11, 'E' => 9, 'F' => 9, 'G' => 10, 'H' => 12] as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $r += 2;
        foreach ([
            'Catatan:',
            'Satu pertemuan = jam-jam berurutan dengan mapel & rombel sama (termasuk yang dipisah istirahat).',
            'Jam yang tergeser upacara/kegiatan dan hari libur tidak dihitung wajib.',
            'Sebagian = jurnal baru diisi untuk sebagian rombel/jam pada pertemuan tersebut.',
        ] as $i => $teks) {
            $sheet->setCellValue("A{$r}", $teks)->mergeCells("A{$r}:H{$r}");
            $sheet->getStyle("A{$r}")->getFont()->setSize(9)->setBold($i === 0)->setItalic($i > 0);
            $r++;
        }

        $this->pasangTandaTangan($sheet, $r + 1, $n);
    }
}
