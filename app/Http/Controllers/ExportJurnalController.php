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

        return $pdf->stream('Jurnal_Mengajar_' . $guru->nama_guru . '_' . $bulan . '_' . $tahun . '.pdf');
    }

    public function cetakExcel(Request $request)
    {
        $bulan = $request->query('bulan', Carbon::now()->format('m'));
        $tahun = $request->query('tahun', Carbon::now()->format('Y'));

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\JurnalExport($bulan, $tahun), 
            'Rekap_Jurnal_Mengajar_' . $bulan . '_' . $tahun . '.xlsx'
        );
    }
}
