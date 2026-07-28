<?php

namespace App\Exports;

use App\Models\Master\MasterTendik;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TendikDataExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return MasterTendik::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'NIP/NIK',
            'Nama',
            'L/P',
            'Tempat Lahir',
            'Tanggal Lahir',
            'No. HP',
            'Email',
            'Alamat',
            'Jabatan',
            'Unit Kerja',
            'Status Kepegawaian',
            'TMT Tugas',
            'Status Personil'
        ];
    }

    public function map($tendik): array
    {
        return [
            $tendik->id,
            $tendik->nip_nik,
            $tendik->nama,
            $tendik->jenis_kelamin,
            $tendik->tempat_lahir,
            $tendik->tanggal_lahir ? $tendik->tanggal_lahir->format('Y-m-d') : '',
            $tendik->no_hp,
            $tendik->email,
            $tendik->alamat,
            $tendik->jabatan?->name ?? $tendik->jabatan?->value,
            $tendik->unit_kerja,
            $tendik->status_kepegawaian?->name ?? $tendik->status_kepegawaian?->value,
            $tendik->tmt_tugas ? $tendik->tmt_tugas->format('Y-m-d') : '',
            $tendik->status?->name ?? $tendik->status?->value,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
