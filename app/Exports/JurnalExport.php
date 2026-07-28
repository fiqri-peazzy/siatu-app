<?php

namespace App\Exports;

use App\Models\JurnalGuru;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class JurnalExport implements FromCollection, WithHeadings, WithMapping
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        return JurnalGuru::with(['guru', 'kelas', 'mapel'])
            ->where('status', 'diisi')
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_ke', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Guru',
            'Mata Pelajaran',
            'Kelas',
            'Jam Ke',
            'Materi',
            'Kegiatan'
        ];
    }

    public function map($jurnal): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            Carbon::parse($jurnal->tanggal)->format('d/m/Y'),
            $jurnal->guru->nama_guru ?? '-',
            $jurnal->mapel->nama_mapel ?? '-',
            $jurnal->kelas->nama_kelas ?? '-',
            $jurnal->jam_ke,
            $jurnal->materi,
            $jurnal->kegiatan,
        ];
    }
}
