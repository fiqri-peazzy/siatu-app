<?php

namespace App\Http\Controllers\Laporan;

use App\Enums\KategoriAbsensi;
use App\Exports\LaporanAbsensiSiswaBulananExport;
use App\Exports\LaporanJurnalBulananExport;
use App\Exports\RekapKehadiranBulananExport;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/** Unduh laporan bulanan berkop resmi dari Pusat Laporan (pimpinan & TU). */
class UnduhLaporanController extends Controller
{
    public const JENIS = ['kehadiran-guru', 'kehadiran-tendik', 'absensi-siswa', 'jurnal'];

    public function __invoke(Request $request, string $jenis)
    {
        abort_unless(in_array($jenis, self::JENIS, true), 404);
        $data = $request->validate([
            'bulan' => ['required', 'date_format:Y-m', 'before_or_equal:'.now()->format('Y-m')],
        ], ['bulan.before_or_equal' => 'Bulan tidak boleh melebihi bulan berjalan.']);

        $bulan = Carbon::createFromFormat('Y-m', $data['bulan'])->startOfMonth();
        $label = $bulan->format('Y-m');

        [$export, $nama] = match ($jenis) {
            'kehadiran-guru' => [new RekapKehadiranBulananExport(KategoriAbsensi::GURU, $bulan), "Rekap_Kehadiran_Guru_{$label}.xlsx"],
            'kehadiran-tendik' => [new RekapKehadiranBulananExport(KategoriAbsensi::TENDIK, $bulan), "Rekap_Kehadiran_Tendik_{$label}.xlsx"],
            'absensi-siswa' => [new LaporanAbsensiSiswaBulananExport($bulan), "Rekap_Kehadiran_Siswa_{$label}.xlsx"],
            'jurnal' => [new LaporanJurnalBulananExport($bulan), "Rekap_Jurnal_Mengajar_{$label}.xlsx"],
        };

        return Excel::download($export, $nama);
    }
}
