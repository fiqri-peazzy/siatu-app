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
        if (! empty($s['tagline'])) {
            $tulis($s['tagline'], 10, false);
        }
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

    /**
     * Untuk export berbasis view (kop teks dari exports/partials/kop.blade.php, 4 baris):
     * pasang logo, garis ganda di bawah kop, dan tanda tangan di bawah data.
     */
    protected function finalisasiDokumen(Worksheet $sheet, ?int $jumlahKolom = null, string $jabatan = 'Kepala Sekolah'): void
    {
        $jumlahKolom ??= Coordinate::columnIndexFromString($sheet->getHighestColumn());
        $akhir = Coordinate::stringFromColumnIndex($jumlahKolom);
        $barisData = $sheet->getHighestRow();

        $logo = public_path('images/logo-removebg-preview.png');
        if (is_file($logo)) {
            $d = new Drawing;
            $d->setName('Logo')->setPath($logo)->setHeight(52)->setCoordinates('A1')->setOffsetX(4)->setOffsetY(2);
            $d->setWorksheet($sheet);
        }
        foreach ([1, 2, 3] as $r) {
            $sheet->getRowDimension($r)->setRowHeight(18);
        }
        $sheet->getStyle("A4:{$akhir}4")->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);
        $sheet->getRowDimension(4)->setRowHeight(6);

        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4)->setFitToWidth(1)->setFitToHeight(0);
        $this->pasangTandaTangan($sheet, $barisData + 2, $jumlahKolom, $jabatan);
    }

    /** Teks 3 baris kop ringkas: [induk, nama sekolah, tagline+alamat+kontak] */
    protected function teksKopRingkas(): array
    {
        $s = config('sekolah');

        return [
            mb_strtoupper(collect(explode('|', (string) ($s['induk'] ?? '')))->map(fn ($b) => trim($b))->filter()->implode(' — ')),
            mb_strtoupper($s['nama'] ?? ''),
            collect([
                $s['tagline'] ?? null,
                collect([$s['alamat'] ?? null, $s['kota'] ?? null, $s['provinsi'] ?? null])->filter()->implode(', '),
                ! empty($s['telepon']) ? 'Telp. '.$s['telepon'] : null,
                $s['email'] ?? null,
                ! empty($s['npsn']) ? 'NPSN '.$s['npsn'] : null,
            ])->filter()->implode(' | '),
        ];
    }

    /** Tulis kop ringkas di baris 1–3 (+ pemisah baris 4) tanpa menggeser data — untuk template import. */
    protected function timpaKopBaris1Sampai4(Worksheet $sheet, int $jumlahKolom, string $judul): void
    {
        $akhir = Coordinate::stringFromColumnIndex(max($jumlahKolom, 6));
        [$induk, $nama, $kontak] = $this->teksKopRingkas();

        // Lepas merge lama yang menyentuh baris 1–3 (bentuknya bisa apa saja di template)
        foreach (array_keys($sheet->getMergeCells()) as $rentang) {
            [$awalSel] = explode(':', $rentang);
            if ((int) preg_replace('/\D/', '', $awalSel) <= 3) {
                $sheet->unmergeCells($rentang);
            }
        }

        foreach ([1 => [$nama.' — '.mb_strtoupper($judul), 13, true], 2 => [$induk, 9, true], 3 => [$kontak, 9, false]] as $r => [$teks, $ukuran, $tebal]) {
            $sheet->setCellValue("A{$r}", $teks);
            $sheet->mergeCells("A{$r}:{$akhir}{$r}");
            $sheet->getStyle("A{$r}")->applyFromArray([
                'font' => ['bold' => $tebal, 'size' => $ukuran],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($r)->setRowHeight(18);
        }
        $sheet->getStyle("A4:{$akhir}4")->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);

        $logo = public_path('images/logo-removebg-preview.png');
        if (is_file($logo)) {
            $d = new Drawing;
            $d->setName('Logo')->setPath($logo)->setHeight(50)->setCoordinates('A1')->setOffsetX(4)->setOffsetY(2);
            $d->setWorksheet($sheet);
        }
    }

    /** Sisipkan 4 baris kop di atas tabel yang sudah ada, lalu logo & tanda tangan — untuk export data master. */
    protected function sisipkanKop(Worksheet $sheet, ?int $jumlahKolom = null): void
    {
        $jumlahKolom ??= Coordinate::columnIndexFromString($sheet->getHighestColumn());
        $akhir = Coordinate::stringFromColumnIndex($jumlahKolom);
        $sheet->insertNewRowBefore(1, 4);
        [$induk, $nama, $kontak] = $this->teksKopRingkas();
        foreach ([1 => [$induk, 10, true], 2 => [$nama, 15, true], 3 => [$kontak, 9, false]] as $r => [$teks, $ukuran, $tebal]) {
            $sheet->setCellValue("A{$r}", $teks);
            $sheet->mergeCells("A{$r}:{$akhir}{$r}");
            $sheet->getStyle("A{$r}")->applyFromArray([
                'font' => ['bold' => $tebal, 'size' => $ukuran, 'color' => ['argb' => 'FF000000']],
                'fill' => ['fillType' => 'none'],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_NONE]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
        }
        $this->finalisasiDokumen($sheet, $jumlahKolom);
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
