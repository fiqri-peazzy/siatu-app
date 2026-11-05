<?php

namespace App\Livewire\Mobile\Tendik;

use App\Enums\JenisAbsenGuru;
use App\Enums\KategoriAbsensi;
use App\Models\AbsensiTendik;
use App\Services\AturanAbsensiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Beranda portal mobile tenaga kependidikan: status absen hari ini,
 * jam absen yang berlaku, dan ringkasan kehadiran bulan ini.
 */
class BerandaTendik extends Component
{
    public function render(AturanAbsensiService $aturan)
    {
        $tendik = Auth::user()->masterTendik;
        abort_unless($tendik, 403, 'Akun belum terhubung dengan data Tenaga Kependidikan.');

        $hariIni = AbsensiTendik::query()
            ->where('tendik_id', $tendik->id)
            ->whereDate('tanggal', today())
            ->get()
            ->keyBy('jenis');

        $bulanIni = AbsensiTendik::query()
            ->where('tendik_id', $tendik->id)
            ->where('jenis', JenisAbsenGuru::MASUK->value)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->get(['status']);

        return view('livewire.mobile.tendik.beranda-tendik', [
            'tendik' => $tendik,
            'absenMasuk' => $hariIni->get(JenisAbsenGuru::MASUK->value),
            'absenPulang' => $hariIni->get(JenisAbsenGuru::PULANG->value),
            'jendela' => $aturan->jendela(KategoriAbsensi::TENDIK, Carbon::now()),
            'ringkasan' => [
                'hadir' => $bulanIni->count(),
                'terlambat' => $bulanIni->where('status', 'terlambat')->count(),
            ],
        ])->layout('components.layouts.pwa', ['tanpaHeader' => true, 'judul' => 'Beranda']);
    }
}
