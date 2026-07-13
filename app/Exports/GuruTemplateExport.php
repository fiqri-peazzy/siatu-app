<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GuruTemplateExport implements FromArray, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['Daftar Guru'], // Row 1
            ['SMAS MUHAMMADIYAH GORONTALO'], // Row 2
            [], // Row 3
            [], // Row 4
            // Row 5: Main Headers
            [
                'No', 'Nama', 'NUPTK', 'JK', 'Tempat Lahir', 'Tanggal Lahir', 'NIP', 'Status Kepegawaian', 
                'Jenis PTK', 'Agama', 'Alamat Jalan', 'RT', 'RW', 'Nama Dusun', 'Desa/Kelurahan', 'Kecamatan', 
                'Kode Pos', 'Telepon', 'HP', 'Email'
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        
        $styleArray = [
            'font' => ['bold' => true],
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

        $sheet->getStyle('A5:T5')->applyFromArray($styleArray);

        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
