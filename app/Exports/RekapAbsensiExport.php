<?php

namespace App\Exports;

use App\Models\Master\MasterGuru;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapAbsensiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $filterStatus;
    protected $search;

    public function __construct($startDate, $endDate, $filterStatus, $search)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->filterStatus = $filterStatus;
        $this->search = $search;
    }

    public function collection()
    {
        $start = $this->startDate;
        $end = $this->endDate;

        $query = MasterGuru::query()
            ->with(['absensis' => function($q) use ($start, $end) {
                $q->whereBetween('tanggal', [$start, $end]);
            }])
            ->when($this->search, function($q) {
                $q->where('nama', 'like', "%{$this->search}%");
            });

        if ($this->filterStatus) {
            if ($this->filterStatus === 'belum_absen') {
                $query->whereDoesntHave('absensis', function($q) use ($start, $end) {
                    $q->whereBetween('tanggal', [$start, $end]);
                });
            } else {
                $query->whereHas('absensis', function($q) use ($start, $end) {
                    $q->whereBetween('tanggal', [$start, $end])
                      ->where('jenis', \App\Enums\JenisAbsenGuru::MASUK->value)
                      ->where('status', $this->filterStatus);
                });
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nama Guru',
            'NIP',
            'Jabatan',
            'Total Hadir',
            'Total Terlambat',
            'Total Izin',
            'Total Sakit',
            'Total Alfa'
        ];
    }

    public function map($guru): array
    {
        $absensisMasuk = $guru->absensis->where('jenis', 'masuk');
        
        $hadir = $absensisMasuk->where('status', 'hadir')->count();
        $terlambat = $absensisMasuk->where('status', 'terlambat')->count();
        $izin = $absensisMasuk->where('status', 'izin')->count();
        $sakit = $absensisMasuk->where('status', 'sakit')->count();
        $alfa = $absensisMasuk->where('status', 'alfa')->count();

        return [
            $guru->nama,
            $guru->nip ?? '-',
            $guru->jabatan ?? '-',
            $hadir,
            $terlambat,
            $izin,
            $sakit,
            $alfa
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
