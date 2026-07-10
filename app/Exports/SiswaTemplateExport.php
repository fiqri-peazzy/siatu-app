<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaTemplateExport implements FromArray, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['Daftar Peserta Didik'], // Row 1
            ['Template Import Siswa'], // Row 2
            [], // Row 3
            [], // Row 4
            // Row 5: Main Headers
            [
                'No', 'Nama', 'NIPD', 'JK', 'NISN', 'Tempat Lahir', 'Tanggal Lahir', 'NIK', 'Agama', 'Alamat', 
                'RT', 'RW', 'Dusun', 'Kelurahan', 'Kecamatan', 'Kode Pos', 'Jenis Tinggal', 'Alat Transportasi', 'Telepon', 'HP', 
                'E-Mail', 'SKHUN', 'Penerima KPS', 'No. KPS', 'Data Ayah', '', '', '', '', '', 
                'Data Ibu', '', '', '', '', '', 'Data Wali', '', '', '', '', '', 
                'Rombel Saat Ini', 'No Peserta Ujian Nasional', 'No Seri Ijazah', 'Penerima KIP', 'Nomor KIP', 'Nama di KIP', 'Nomor KKS', 'No Registrasi Akta Lahir', 
                'Bank', 'Nomor Rekening Bank', 'Rekening Atas Nama', 'Layak PIP (usulan dari sekolah)', 'Alasan Layak PIP', 'Kebutuhan Khusus', 'Sekolah Asal', 'Anak ke-berapa', 'Lintang', 'Bujur', 
                'No KK', 'Berat Badan', 'Tinggi Badan', 'Lingkar Kepala', 'Jml. Saudara Kandung', 'Jarak Rumah ke Sekolah (KM)'
            ],
            // Row 6: Sub Headers
            [
                '', '', '', '', '', '', '', '', '', '', 
                '', '', '', '', '', '', '', '', '', '', 
                '', '', '', '', 'Nama', 'Tahun Lahir', 'Jenjang Pendidikan', 'Pekerjaan', 'Penghasilan', 'NIK', 
                'Nama', 'Tahun Lahir', 'Jenjang Pendidikan', 'Pekerjaan', 'Penghasilan', 'NIK', 'Nama', 'Tahun Lahir', 'Jenjang Pendidikan', 'Pekerjaan', 'Penghasilan', 'NIK', 
                '', '', '', '', '', '', '', '', 
                '', '', '', '', '', '', '', '', '', '', 
                '', '', '', '', '', ''
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for Title
        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        
        // Merge cells for main headers that have sub headers
        $sheet->mergeCells('Y5:AD5'); // Data Ayah (col 24 to 29 -> Y to AD)
        $sheet->mergeCells('AE5:AJ5'); // Data Ibu (col 30 to 35 -> AE to AJ)
        $sheet->mergeCells('AK5:AP5'); // Data Wali (col 36 to 41 -> AK to AP)

        // Merge cells vertically for headers without subheaders
        // Cols 0 to 23 -> A to X
        $colsBefore = range('A', 'X');
        foreach ($colsBefore as $col) {
            $sheet->mergeCells("{$col}5:{$col}6");
        }

        // Cols 42 to 65 -> AQ to BN
        // Since range() on letters can go up to Z, then AA to ZZ, let's generate it
        $colsAfter = [];
        $start = 'AQ';
        while ($start !== 'BO') { // BO is column 66 (index 66)
            $colsAfter[] = $start;
            $start++;
        }
        
        foreach ($colsAfter as $col) {
            $sheet->mergeCells("{$col}5:{$col}6");
        }

        $styleArray = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FFEFEFEF']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $sheet->getStyle('A5:BN6')->applyFromArray($styleArray);

        // Adjust some row heights and column widths if needed
        $sheet->getRowDimension(5)->setRowHeight(30);
        $sheet->getRowDimension(6)->setRowHeight(30);

        return [
            1    => ['font' => ['bold' => true, 'size' => 14]],
            2    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

