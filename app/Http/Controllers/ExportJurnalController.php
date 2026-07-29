<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JurnalGuru;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExportJurnalController extends Controller
{
    public function cetakPdfGuru(Request $request)
    {
        $bulan = $request->query('bulan', Carbon::now()->format('m'));
        $tahun = $request->query('tahun', Carbon::now()->format('Y'));
        $guruId = Auth::user()->guru_id ?? Auth::user()->guru->id ?? null;

        if (!$guruId) abort(403);

        $jurnals = JurnalGuru::with(['kelas', 'mapel'])
            ->where('guru_id', $guruId)
            ->where('status', 'diisi')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_ke', 'asc')
            ->get();

        $guru = Auth::user()->guru ?? \App\Models\Master\MasterGuru::find($guruId);

        $pdf = Pdf::loadView('pdf.jurnal-mengajar', [
            'jurnals' => $jurnals,
            'guru' => $guru,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);

        return $pdf->stream('Jurnal_Mengajar_' . $guru->nama . '_' . $bulan . '_' . $tahun . '.pdf');
    }

    public function cetakExcel(Request $request)
    {
        $start_date = $request->query('start_date', Carbon::now()->format('Y-m-d'));
        $end_date = $request->query('end_date', Carbon::now()->format('Y-m-d'));
        $guru_id = $request->query('guru');
        $mapel_id = $request->query('mapel');
        $kelas_id = $request->query('kelas');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\JurnalExport($start_date, $end_date, $guru_id, $mapel_id, $kelas_id), 
            'Rekap_Jurnal_Mengajar_' . $start_date . '_sd_' . $end_date . '.xlsx'
        );
    }
}
