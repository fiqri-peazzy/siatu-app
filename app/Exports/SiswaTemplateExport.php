<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaTemplateExport implements FromArray, WithStyles
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
        $sheet->mergeCells('Y5:AD5'); // Data Ayah
        $sheet->mergeCells('AE5:AJ5'); // Data Ibu
        $sheet->mergeCells('AK5:AP5'); // Data Wali

        return [
            1    => ['font' => ['bold' => true, 'size' => 14]],
            2    => ['font' => ['bold' => true, 'size' => 12]],
            5    => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FFEFEFEF']]],
            6    => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FFEFEFEF']]],
        ];
    }
}
