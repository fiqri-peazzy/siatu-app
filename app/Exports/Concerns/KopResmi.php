<?php

namespace App\Exports\Concerns;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Kop resmi sekolah + blok tanda tangan untuk semua dokumen Excel
 * (export laporan & template import). Identitas dari config/sekolah.php.
 */
trait KopResmi
{
    /** Warna dokumen (ARGB) — selaras design system: navy & gold */
    protected string $warnaJudulTabel = 'FF0E2A55';

    /**
     * Tulis kop di baris paling atas.
     *
     * @return int baris pertama yang kosong setelah kop + judul
     */
    protected function pasangKop(Worksheet $sheet, int $jumlahKolom, string $judul, ?string $subjudul = null): int
    {
        $s = config('sekolah');
        $akhir = Coordinate::stringFromColumnIndex(max($jumlahKolom, 6));
        $baris = 1;

        $tulis = function (string $teks, int $ukuran, bool $tebal = true) use ($sheet, $akhir, &$baris) {
            $sheet->setCellValue("A{$baris}", $teks);
            $sheet->mergeCells("A{$baris}:{$akhir}{$baris}");
            $sheet->getStyle("A{$baris}")->applyFromArray([
                'font' => ['bold' => $tebal, 'size' => $ukuran, 'name' => 'Arial'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $baris++;
        };

        foreach (array_filter(explode('|', (string) ($s['induk'] ?? ''))) as $induk) {
            $tulis(mb_strtoupper(trim($induk)), 11);
        }
        $tulis(mb_strtoupper($s['nama'] ?? 'SEKOLAH'), 15);
        if (! empty($s['akreditasi'])) {
            $tulis('TERAKREDITASI '.mb_strtoupper($s['akreditasi']), 10);
        }

        $alamat = collect([$s['alamat'] ?? null, $s['kota'] ?? null, $s['provinsi'] ?? null])->filter()->implode(', ');
        $kontak = collect([
            ! empty($s['telepon']) ? 'Telp. '.$s['telepon'] : null,
            ! empty($s['email']) ? 'Email: '.$s['email'] : null,
            ! empty($s['npsn']) ? 'NPSN: '.$s['npsn'] : null,
        ])->filter()->implode('  •  ');
        if ($alamat) {
            $tulis($alamat, 9, false);
        }
        if ($kontak) {
            $tulis($kontak, 9, false);
        }

        // Logo di kiri kop
        $logo = public_path('images/logo-removebg-preview.png');
        if (is_file($logo)) {
            $d = new Drawing;
            $d->setName('Logo')->setPath($logo)->setHeight(64)->setCoordinates('A1')->setOffsetX(6)->setOffsetY(4);
            $d->setWorksheet($sheet);
        }
        for ($r = 1; $r < $baris; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(16);
        }

        // Garis ganda penutup kop
        $sheet->getStyle("A{$baris}:{$akhir}{$baris}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);
        $sheet->getRowDimension($baris)->setRowHeight(6);
        $baris++;

        $tulis(mb_strtoupper($judul), 12);
        if ($subjudul) {
            $tulis($subjudul, 10, false);
        }

        // Cetak: muat satu halaman lebar, kop diulang di tiap halaman
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4)->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.4)->setBottom(0.4)->setLeft(0.3)->setRight(0.3);

        return $baris + 1;
    }

    /** Gaya baris judul kolom tabel */
    protected function gayaJudulTabel(Worksheet $sheet, string $rentang): void
    {
        $sheet->getStyle($rentang)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 9],
            'fill' => ['fillType' => 'solid', 'color' => ['argb' => $this->warnaJudulTabel]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF94A3B8']]],
        ]);
    }

    /** Garis tipis seluruh sel tabel */
    protected function gayaIsiTabel(Worksheet $sheet, string $rentang): void
    {
        $sheet->getStyle($rentang)->applyFromArray([
            'font' => ['size' => 9],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
    }

    /** Blok tanda tangan Kepala Sekolah di kanan bawah */
    protected function pasangTandaTangan(Worksheet $sheet, int $baris, int $jumlahKolom, string $jabatan = 'Kepala Sekolah'): void
    {
        $s = config('sekolah');
        $mulai = Coordinate::stringFromColumnIndex(max(1, $jumlahKolom - 3));
        $akhir = Coordinate::stringFromColumnIndex(max($jumlahKolom, 4));
        $isi = [
            ($s['kota'] ?: 'Gorontalo').', '.now()->locale('id')->isoFormat('D MMMM YYYY'),
            $jabatan.',',
            '', '', '',
            $jabatan === 'Kepala Sekolah' ? ($s['kepala'] ?: '...........................') : ($s['kepala_tu'] ?: '...........................'),
            'NIP. '.(($jabatan === 'Kepala Sekolah' ? $s['nip_kepala'] : $s['nip_ka_tu']) ?: '-'),
        ];
        foreach ($isi as $i => $teks) {
            $r = $baris + $i;
            $sheet->setCellValue("{$mulai}{$r}", $teks);
            $sheet->mergeCells("{$mulai}{$r}:{$akhir}{$r}");
            $sheet->getStyle("{$mulai}{$r}")->applyFromArray([
                'font' => ['size' => 10, 'bold' => $i === 5, 'underline' => $i === 5],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }
    }
}
