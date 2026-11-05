<?php

namespace App\Exports;

use App\Models\Master\MasterGuru;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GuruExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, \Maatwebsite\Excel\Concerns\WithEvents
{
    use \App\Exports\Concerns\KopResmi;

    public function registerEvents(): array
    {
        return [\Maatwebsite\Excel\Events\AfterSheet::class => fn ($e) => $this->sisipkanKop($e->sheet->getDelegate(), 13)];
    }

    public function collection()
    {
        return MasterGuru::with('mapel')->orderBy('nama', 'asc')->get();
    }

    public function map($guru): array
    {
        return [
            $guru->nip ?? '-',
            $guru->nuptk ?? '-',
            $guru->nama,
            $guru->jenis_kelamin == 'L' ? 'Laki-laki' : ($guru->jenis_kelamin == 'P' ? 'Perempuan' : '-'),
            $guru->tempat_lahir ?? '-',
            $guru->tanggal_lahir ? $guru->tanggal_lahir->format('d/m/Y') : '-',
            $guru->jabatan ?? '-',
            $guru->mapel ? $guru->mapel->nama : '-',
            // Kolom ber-cast Enum → label teks (Enum tidak bisa langsung ditulis ke sel)
            $guru->status_kepegawaian?->label() ?? '-',
            $guru->no_hp ?? '-',
            $guru->email ?? '-',
            $guru->alamat ?? '-',
            $guru->status?->label() ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            ['DATA GURU'],
            [],
            [
                'NIP',
                'NUPTK',
                'Nama Lengkap',
                'Jenis Kelamin',
                'Tempat Lahir',
                'Tanggal Lahir',
                'Jabatan',
                'Mapel Utama',
                'Status Kepegawaian',
                'No. HP',
                'Email',
                'Alamat',
                'Status'
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:M1');
        
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A3:M3')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FF4F46E5'], 
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        $highestRow = $sheet->getHighestRow();
        
        if ($highestRow >= 3) {
            $sheet->getStyle('A3:M' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);
        }

        return [];
    }
}
