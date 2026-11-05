<?php

namespace App\Http\Controllers\Akademik;

use App\Enums\KategoriAbsensi;
use App\Exports\RekapKehadiranBulananExport;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

/** Unduh rekap kehadiran bulanan (2 sheet, kop resmi) untuk guru atau tendik. */
class ExportRekapKehadiranController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'kategori' => ['required', Rule::in(KategoriAbsensi::values())],
            'bulan' => ['required', 'date_format:Y-m', 'before_or_equal:'.now()->format('Y-m')],
        ], [
            'bulan.before_or_equal' => 'Bulan tidak boleh melebihi bulan berjalan.',
        ]);

        $kategori = KategoriAbsensi::from($data['kategori']);
        $bulan = Carbon::createFromFormat('Y-m', $data['bulan'])->startOfMonth();

        return Excel::download(
            new RekapKehadiranBulananExport($kategori, $bulan),
            sprintf('Rekap_Kehadiran_%s_%s.xlsx', ucfirst($kategori->value), $bulan->format('Y-m'))
        );
    }
}
