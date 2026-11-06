<?php

namespace App\Exports\Sheets;

use App\Exports\Concerns\KopResmi;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/** Daftar jurnal mengajar yang terisi dalam satu bulan (satu baris per pertemuan). */
class DetailJurnalSheet implements FromArray, WithEvents, WithTitle
{
    use KopResmi;

    private const JUDUL = ['No', 'Hari, Tanggal', 'Guru', 'Mata Pelajaran', 'Rombel', 'Jam ke', 'Materi', 'Kegiatan Pembelajaran', 'Hadir', 'Tidak Hadir'];

    public function __construct(private array $data) {}

    public function title(): string
    {
        return 'Daftar Jurnal';
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
        $r = $this->pasangKop($sheet, $n, 'Daftar Jurnal Mengajar', 'Bulan '.$this->data['bulan']->locale('id')->isoFormat('MMMM YYYY'));

        $sheet->fromArray(self::JUDUL, null, "A{$r}");
        $this->gayaJudulTabel($sheet, "A{$r}:J{$r}");
        $sheet->freezePane('A'.($r + 1));
        $barisJudul = $r;
        $awal = ++$r;

        foreach ($this->data['detail'] as $i => $d) {
            $sheet->fromArray([
                $i + 1, $d['tanggal']->locale('id')->isoFormat('ddd, D MMM YYYY'), $d['guru'], $d['mapel'], $d['kelas'],
                $d['jam'], $d['materi'], $d['kegiatan'], $d['hadir'] ?? '-', $d['tidak_hadir'] ?: 0,
            ], null, "A{$r}", true);
            $r++;
        }
        if ($r === $awal) {
            $sheet->setCellValue("A{$r}", 'Belum ada jurnal yang diisi pada bulan ini.')->mergeCells("A{$r}:J{$r}");
            $r++;
        }

        $this->gayaIsiTabel($sheet, "A{$awal}:J".($r - 1));
        $sheet->getStyle("A{$awal}:J".($r - 1))->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
        $sheet->getStyle("A{$awal}:A".($r - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$awal}:F".($r - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("I{$awal}:J".($r - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        foreach (['A' => 4, 'B' => 17, 'C' => 24, 'D' => 22, 'E' => 16, 'F' => 7, 'G' => 34, 'H' => 34, 'I' => 7, 'J' => 9] as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->pasangTandaTangan($sheet, $r + 1, $n);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($barisJudul, $barisJudul);
    }
}
