<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Master\MasterTahunAjaran;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterMapel;
use App\Models\Master\MasterSiswa;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekapAbsensiSiswaExport implements FromView, ShouldAutoSize, WithEvents
{
    use \App\Exports\Concerns\KopResmi;

    protected $tahun_ajaran_id;
    protected $kelas_id;
    protected $mapel_id;
    protected $tipe_waktu;
    protected $tanggal_harian;
    protected $bulan_bulanan;

    public function __construct($tahun_ajaran_id, $kelas_id, $mapel_id, $tipe_waktu, $tanggal_harian, $bulan_bulanan)
    {
        $this->tahun_ajaran_id = $tahun_ajaran_id;
        $this->kelas_id = $kelas_id;
        $this->mapel_id = $mapel_id;
        $this->tipe_waktu = $tipe_waktu;
        $this->tanggal_harian = $tanggal_harian;
        $this->bulan_bulanan = $bulan_bulanan;
    }

    public function view(): View
    {
        $ta = MasterTahunAjaran::find($this->tahun_ajaran_id);
        $kelas = MasterKelas::find($this->kelas_id);
        $mapelName = 'Semua Mapel';
        
        if ($this->mapel_id && $this->mapel_id != 'semua') {
            $mapelModel = MasterMapel::find($this->mapel_id);
            if ($mapelModel) $mapelName = $mapelModel->nama;
        }

        $startDate = null;
        $endDate = null;
        $waktuLabel = '';

        if ($this->tipe_waktu == 'hari') {
            $startDate = $this->tanggal_harian;
            $endDate = $this->tanggal_harian;
            $waktuLabel = Carbon::parse($this->tanggal_harian)->translatedFormat('d F Y');
        } elseif ($this->tipe_waktu == 'bulan') {
            if ($this->bulan_bulanan) {
                $startDate = $this->bulan_bulanan . '-01';
                $endDate = date('Y-m-t', strtotime($startDate));
                $waktuLabel = 'Bulan ' . Carbon::parse($startDate)->translatedFormat('F Y');
            }
        } elseif ($this->tipe_waktu == 'semester') {
            if ($ta) {
                if (strtolower($ta->semester) == 'ganjil' || $ta->semester == 1) {
                    $startDate = date('Y', strtotime($ta->mulai)) . '-07-01';
                    $endDate = date('Y', strtotime($ta->mulai)) . '-12-31';
                } else {
                    $startDate = date('Y', strtotime($ta->selesai)) . '-01-01';
                    $endDate = date('Y', strtotime($ta->selesai)) . '-06-30';
                }
                $waktuLabel = 'Semester ' . ucfirst($ta->semester) . ' Tahun Ajaran ' . $ta->nama;
            }
        }

        $siswas = MasterSiswa::where('kelas_id', $this->kelas_id)
            ->where('status', 'aktif')
            ->orderBy('nama', 'asc')
            ->get();

        $query = DB::table('absensi_siswas')
            ->join('jurnal_gurus', function ($join) {
                $join->on('absensi_siswas.kelas_id', '=', 'jurnal_gurus.kelas_id')
                     ->on('absensi_siswas.tanggal', '=', 'jurnal_gurus.tanggal')
                     ->on('absensi_siswas.jam_ke', '=', 'jurnal_gurus.jam_ke');
            })
            ->where('absensi_siswas.kelas_id', $this->kelas_id);
            
        if ($startDate && $endDate) {
            $query->whereBetween('absensi_siswas.tanggal', [$startDate, $endDate]);
        }

        if ($this->mapel_id && $this->mapel_id != 'semua') {
            $query->where('jurnal_gurus.mapel_id', $this->mapel_id);
        }

        $absensiRaw = $query->select(
            'absensi_siswas.siswa_id',
            'absensi_siswas.status',
            'absensi_siswas.tanggal',
            'jurnal_gurus.mapel_id'
        )->get();

        $data = [];
        $tanggals = [];

        foreach ($siswas as $siswa) {
            $data[$siswa->id] = [
                'siswa' => $siswa,
                'stats' => ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alfa' => 0],
                'per_tanggal' => []
            ];
        }

        foreach ($absensiRaw as $ab) {
            if (!isset($data[$ab->siswa_id])) continue;
            
            $status = strtolower($ab->status);
            if (isset($data[$ab->siswa_id]['stats'][$status])) {
                $data[$ab->siswa_id]['stats'][$status]++;
            }

            if ($this->mapel_id && $this->mapel_id != 'semua') {
                $data[$ab->siswa_id]['per_tanggal'][$ab->tanggal] = $status;
                $tanggals[$ab->tanggal] = true;
            }
        }

        $kolomTanggal = [];
        if ($this->mapel_id && $this->mapel_id != 'semua') {
            $kolomTanggal = array_keys($tanggals);
            sort($kolomTanggal);
        }

        return view('exports.rekap-absensi-siswa', [
            'rekapData' => $data,
            'kolomTanggal' => $kolomTanggal,
            'kelas' => $kelas,
            'ta' => $ta,
            'mapelName' => $mapelName,
            'waktuLabel' => $waktuLabel,
            'isSemuaMapel' => (!$this->mapel_id || $this->mapel_id == 'semua')
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Style for Header Table
                $event->sheet->getDelegate()->getStyle('A1:Z5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);
                
                // Try to find the boundaries of the main table
                // Let's assume table starts at row 8 or 9.
                // A very simple dynamic style apply for the entire sheet for borders on cells with data.
                $highestRow = $event->sheet->getHighestRow();
                $highestCol = $event->sheet->getHighestColumn();
                
                // Border for the data table (assuming data table starts at row 7)
                if ($highestRow >= 7) {
                    $event->sheet->getDelegate()->getStyle('A7:'.$highestCol.$highestRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ]);
                    
                    // Header table (row 7-8) centering and bold
                    $event->sheet->getDelegate()->getStyle('A7:'.$highestCol.'8')->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ]
                    ]);
                }

                // Logo, garis ganda kop, dan tanda tangan Kepala Sekolah
                $this->finalisasiDokumen($event->sheet->getDelegate());
            },
        ];
    }
}
