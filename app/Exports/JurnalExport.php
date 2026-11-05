<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\JurnalGuru;
use Carbon\Carbon;

class JurnalExport implements FromView, ShouldAutoSize, WithEvents
{
    use \App\Exports\Concerns\KopResmi;

    protected $start_date;
    protected $end_date;
    protected $guru_id;
    protected $mapel_id;
    protected $kelas_id;

    public function __construct($start_date, $end_date, $guru_id, $mapel_id, $kelas_id)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->guru_id = $guru_id;
        $this->mapel_id = $mapel_id;
        $this->kelas_id = $kelas_id;
    }

    public function view(): View
    {
        $query = JurnalGuru::with(['guru', 'kelas', 'mapel'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_ke', 'asc');

        if ($this->start_date && $this->end_date) {
            $query->whereBetween('tanggal', [$this->start_date, $this->end_date]);
        }
        if ($this->guru_id) {
            $query->where('guru_id', $this->guru_id);
        }
        if ($this->mapel_id) {
            $query->where('mapel_id', $this->mapel_id);
        }
        if ($this->kelas_id) {
            $query->where('kelas_id', $this->kelas_id);
        }

        $allJurnals = $query->get();

        // Group by same class and teacher on same day for continuous hours
        $grouped = collect();
        $lastGroup = null;

        foreach ($allJurnals as $j) {
            if ($lastGroup && 
                $lastGroup->guru_id == $j->guru_id && 
                $lastGroup->kelas_id == $j->kelas_id && 
                $lastGroup->mapel_id == $j->mapel_id && 
                $lastGroup->tanggal->isSameDay($j->tanggal) && 
                $lastGroup->jam_selesai + 1 == $j->jam_ke) {
                
                $lastGroup->jam_selesai = $j->jam_ke;
            } else {
                if ($lastGroup) {
                    $grouped->push($lastGroup);
                }
                
                $lastGroup = clone $j;
                $lastGroup->jam_mulai = $j->jam_ke;
                $lastGroup->jam_selesai = $j->jam_ke;
            }
        }
        
        if ($lastGroup) {
            $grouped->push($lastGroup);
        }

        return view('exports.jurnal-mengajar', [
            'jurnals' => $grouped,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Header style
                $event->sheet->getDelegate()->getStyle('A1:K5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);
                
                $highestRow = $event->sheet->getHighestRow();
                $highestCol = 'K'; // Assuming 11 columns (A to K)
                
                if ($highestRow >= 7) {
                    // Borders
                    $event->sheet->getDelegate()->getStyle('A7:'.$highestCol.$highestRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                            'wrapText' => true,
                        ]
                    ]);
                    
                    // Center and bold for table header
                    $event->sheet->getDelegate()->getStyle('A7:'.$highestCol.'7')->applyFromArray([
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

                // Logo, garis ganda kop, dan tanda tangan Kepala Sekolah
                $this->finalisasiDokumen($event->sheet->getDelegate());
            },
        ];
    }
}
