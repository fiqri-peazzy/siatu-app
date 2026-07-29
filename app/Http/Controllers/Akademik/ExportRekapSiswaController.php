<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapAbsensiSiswaExport;
use App\Models\Master\MasterTahunAjaran;
use App\Models\Master\MasterKelas;

class ExportRekapSiswaController extends Controller
{
    public function export(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required',
            'kelas_id' => 'required',
        ]);

        $kelas = MasterKelas::findOrFail($request->kelas_id);
        $ta = MasterTahunAjaran::findOrFail($request->tahun_ajaran_id);

        $fileName = 'Rekap_Absensi_' . str_replace(' ', '_', $kelas->nama) . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new RekapAbsensiSiswaExport(
            $request->tahun_ajaran_id,
            $request->kelas_id,
            $request->mapel_id,
            $request->tipe_waktu,
            $request->tanggal_harian,
            $request->bulan_bulanan
        ), $fileName);
    }
}
