<?php

namespace App\Exports\Sheets;

use App\Exports\Concerns\KopResmi;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/** Ringkasan kehadiran siswa per rombel dalam satu bulan. */
class RingkasanAbsensiSiswaSheet implements FromArray, WithEvents, WithTitle
{
    use KopResmi;

    private const JUDUL = ['No', 'Rombel', 'Wali Kelas', 'Siswa', 'Hari Tercatat', 'Hadir', 'Izin', 'Sakit', 'Alfa', 'Jam Alfa', '% Hadir', 'Siswa Alfa Berulang'];

    public function __construct(private array $data) {}

    public function title(): string
    {
        return 'Ringkasan';
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
        $r = $this->pasangKop($sheet, $n, 'Rekap Kehadiran Siswa', "Bulan {$bulan} · {$this->data['hari_sekolah']} hari sekolah");

        $sheet->fromArray(self::JUDUL, null, "A{$r}");
        $this->gayaJudulTabel($sheet, "A{$r}:L{$r}");
        $awal = ++$r;

        foreach ($this->data['kelas'] as $i => $k) {
            $s = $k['ringkasan'];
            $sheet->fromArray([
                $i + 1, $k['nama'], $k['wali'] ?? '-', $s['jumlah_siswa'], $s['hari_tercatat'],
                $s['H'], $s['I'], $s['S'], $s['A'], $s['jam_alfa'],
                $s['persen'] === null ? '-' : $s['persen'] / 100,
                $s['alfa_berulang'] ? implode(', ', $s['alfa_berulang']) : '-',
            ], null, "A{$r}", true);
            if ($s['persen'] !== null) {
                $sheet->getStyle("K{$r}")->getNumberFormat()->setFormatCode('0.0%');
                $warna = $s['persen'] >= 90 ? 'FFE7F3EC' : ($s['persen'] >= 75 ? 'FFFDF1DD' : 'FFFBE1DE');
                $sheet->getStyle("K{$r}")->getFill()->setFillType('solid')->getStartColor()->setARGB($warna);
            }
            $r++;
        }
        if ($r === $awal) {
            $sheet->setCellValue("A{$r}", 'Belum ada rombel aktif yang berisi siswa.');
            $sheet->mergeCells("A{$r}:L{$r}");
            $r++;
        }

        $this->gayaIsiTabel($sheet, "A{$awal}:L".($r - 1));
        $sheet->getStyle("D{$awal}:K".($r - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$awal}:A".($r - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("L{$awal}:L".($r - 1))->getAlignment()->setWrapText(true);
        foreach (['A' => 4, 'B' => 16, 'C' => 26, 'D' => 7, 'E' => 9, 'F' => 8, 'G' => 7, 'H' => 7, 'I' => 7, 'J' => 8, 'K' => 9, 'L' => 36] as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $r++;
        $catatan = [
            'Catatan:',
            'Hadir/Izin/Sakit/Alfa dihitung per siswa per hari; status harian = status terbanyak pada jam pelajaran hari itu.',
            'Jam Alfa = jumlah jam pelajaran berstatus alfa (menangkap siswa yang tidak hadir di sebagian jam).',
            "Siswa alfa berulang = alfa ≥ {$this->data['ambang']} hari dalam bulan ini (ambang di Pengaturan Sistem).",
        ];
        foreach ($catatan as $i => $t) {
            $sheet->setCellValue("A{$r}", $t)->mergeCells("A{$r}:L{$r}");
            $sheet->getStyle("A{$r}")->getFont()->setSize(9)->setBold($i === 0)->setItalic($i > 0);
            $r++;
        }

        $this->pasangTandaTangan($sheet, $r + 1, $n);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    }
}
