<?php

namespace App\Livewire\Laporan;

use App\Models\AbsensiSiswa;
use App\Models\JurnalGuru;
use App\Services\LaporanBulananService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

/** Pusat Laporan bulanan untuk Kepala Sekolah & TU: pilih bulan, unduh rekap Excel berkop. */
#[Layout('components.layouts.app')]
class PusatLaporan extends Component
{
    #[Url(as: 'b')]
    public int $filterBulan;

    #[Url(as: 't')]
    public int $filterTahun;

    public function mount(): void
    {
        $this->filterBulan ??= now()->month;
        $this->filterTahun ??= now()->year;
        $this->rapikan();
    }

    public function geserBulan(int $arah): void
    {
        $tgl = Carbon::create($this->filterTahun, $this->filterBulan, 1)->addMonths($arah);
        $this->filterBulan = $tgl->month;
        $this->filterTahun = $tgl->year;
        $this->rapikan();
    }

    /** Tidak boleh melewati bulan berjalan */
    private function rapikan(): void
    {
        $tgl = Carbon::create($this->filterTahun, max(1, min(12, $this->filterBulan)), 1);
        if ($tgl->gt(now()->startOfMonth())) {
            $tgl = now()->startOfMonth();
        }
        $this->filterBulan = $tgl->month;
        $this->filterTahun = $tgl->year;
    }

    public function render(LaporanBulananService $laporan)
    {
        $bulan = Carbon::create($this->filterTahun, $this->filterBulan, 1);
        $rentang = [$bulan->copy()->startOfMonth()->toDateString(), $bulan->copy()->endOfMonth()->toDateString()];
        $kalender = collect($laporan->kalender($bulan));

        return view('livewire.laporan.pusat-laporan', [
            'bulan' => $bulan,
            'kodeBulan' => $bulan->format('Y-m'),
            'info' => [
                'hari_sekolah' => $kalender->where('off', false)->count(),
                'hari_berjalan' => $kalender->where('off', false)->filter(fn ($h) => $h['tgl']->lte(today()))->count(),
                'libur' => $kalender->filter(fn ($h) => $h['keterangan'] && $h['keterangan'] !== 'Bukan hari sekolah')
                    ->map(fn ($h) => $h['tgl']->isoFormat('D').' '.$h['keterangan'])->values()->all(),
                'absen_siswa' => AbsensiSiswa::whereBetween('tanggal', $rentang)->distinct()->count('tanggal'),
                'jurnal' => JurnalGuru::whereBetween('tanggal', $rentang)->where('status', 'diisi')->count(),
            ],
        ]);
    }
}
