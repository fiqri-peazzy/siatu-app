<?php

namespace App\Exports\Sheets;

use App\Exports\Concerns\KopResmi;
use App\Services\RekapKehadiranService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/** Detail per pegawai per hari: hari, tanggal, jam datang, jam pulang, status, keterangan. */
class DetailKehadiranSheet implements FromArray, WithEvents, WithTitle
{
    use KopResmi;

    public function __construct(private array $data) {}

    public function title(): string
    {
        return 'Detail Harian';
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
        $label = $this->data['kategori']->label();
        $r = $this->pasangKop($sheet, 9, "Detail Kehadiran Harian {$label}",
            'Bulan '.$this->data['bulan']->locale('id')->isoFormat('MMMM YYYY'));

        $judul = ['No', 'Nama', 'NIP / NUPTK', 'Hari', 'Tanggal', 'Jam Datang', 'Jam Pulang', 'Status', 'Keterangan'];
        foreach ($judul as $i => $j) {
            $sheet->setCellValue(chr(65 + $i).$r, $j);
        }
        $this->gayaJudulTabel($sheet, "A{$r}:I{$r}");
        $sheet->freezePane('A'.($r + 1));
        $barisJudul = $r++;

        $no = 1;
        foreach ($this->data['pegawai'] as $p) {
            foreach ($p['detail'] as $d) {
                $sheet->setCellValue("A{$r}", $no++)->setCellValue("B{$r}", $p['nama']);
                $sheet->setCellValueExplicit("C{$r}", (string) $p['nip'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue("D{$r}", $d['tanggal']->locale('id')->isoFormat('dddd'));
                $sheet->setCellValue("E{$r}", $d['tanggal']->format('d/m/Y'));
                $sheet->setCellValue("F{$r}", $d['datang'] ?? '-');
                $sheet->setCellValue("G{$r}", $d['pulang'] ?? '-');
                $sheet->setCellValue("H{$r}", RekapKehadiranService::KODE[$d['kode']][0] ?? '-');
                $sheet->setCellValue("I{$r}", $d['keterangan']);
                if ($warna = RekapKehadiranService::KODE[$d['kode']][1] ?? null) {
                    $sheet->getStyle("H{$r}")->getFill()->setFillType('solid')->getStartColor()->setARGB($warna);
                }
                $r++;
            }
        }

        if ($r === $barisJudul + 1) {
            $sheet->setCellValue("A{$r}", 'Belum ada data kehadiran pada bulan ini.')->mergeCells("A{$r}:I{$r}");
            $r++;
        }

        $this->gayaIsiTabel($sheet, 'A'.($barisJudul + 1).':I'.($r - 1));
        $sheet->getStyle('A'.($barisJudul + 1).':A'.($r - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D'.($barisJudul + 1).':H'.($r - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        foreach (['A' => 4, 'B' => 28, 'C' => 20, 'D' => 9, 'E' => 11, 'F' => 10, 'G' => 10, 'H' => 22, 'I' => 46] as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }
        $sheet->getStyle('I'.($barisJudul + 1).':I'.($r - 1))->getAlignment()->setWrapText(true);
        $sheet->setAutoFilter("A{$barisJudul}:I".($r - 1));

        $this->pasangTandaTangan($sheet, $r + 2, 9);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($barisJudul, $barisJudul);
    }
}
