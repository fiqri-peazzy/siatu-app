<?php

namespace App\Exports;

use App\Models\Master\MasterTendik;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class RekapAbsensiTendikExport implements FromView, ShouldAutoSize, WithEvents
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

    public function view(): View
    {
        $start = $this->startDate;
        $end = $this->endDate;

        $query = MasterTendik::query()
            ->with(['absensis' => function($q) use ($start, $end) {
                if ($start && $end) {
                    $q->whereBetween('tanggal', [$start, $end]);
                }
            }])
            ->when($this->search, function($q) {
                $q->where('nama', 'like', "%{$this->search}%");
            });

        if ($this->filterStatus) {
            if ($this->filterStatus === 'belum_absen') {
                $query->whereDoesntHave('absensis', function($q) use ($start, $end) {
                    if ($start && $end) {
                        $q->whereBetween('tanggal', [$start, $end]);
                    }
                });
            } else {
                $query->whereHas('absensis', function($q) use ($start, $end) {
                    $q->where('jenis', \App\Enums\JenisAbsenGuru::MASUK->value)
                      ->where('status', $this->filterStatus);
                      
                    if ($start && $end) {
                        $q->whereBetween('tanggal', [$start, $end]);
                    }
                });
            }
        }

        return view('exports.rekap-absensi-tendik', [
            'data' => $query->get(),
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Style untuk Header Kop Surat
                $event->sheet->getDelegate()->getStyle('A1:I5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);
                
                $highestRow = $event->sheet->getHighestRow();
                $highestCol = 'I'; // 9 Kolom (A sampai I)
                
                // Style untuk tabel (mulai dari baris ke-9)
                if ($highestRow >= 9) {
                    // Borders untuk seluruh cell pada tabel
                    $event->sheet->getDelegate()->getStyle('A9:'.$highestCol.$highestRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ]
                    ]);
                    
                    // Background dan bold khusus untuk header tabel (baris 9)
                    $event->sheet->getDelegate()->getStyle('A9:'.$highestCol.'9')->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'argb' => 'FFE5E7EB', // bg-gray-200
                            ],
                        ],
                    ]);
                }
            },
        ];
    }
}
