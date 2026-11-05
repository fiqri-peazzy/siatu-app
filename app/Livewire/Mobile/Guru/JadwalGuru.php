<?php

namespace App\Livewire\Mobile\Guru;

use App\Enums\Hari;
use App\Services\JadwalService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Jadwal mengajar guru (jadwal v2): strip hari minggu ini + timeline harian
 * lengkap (pelajaran, istirahat, upacara, kegiatan) dengan status jurnal.
 */
class JadwalGuru extends Component
{
    #[Url(as: 'tgl', except: '')]
    public string $tanggal = '';

    public function mount(): void
    {
        $this->tanggal = $this->tanggalValid($this->tanggal);
    }

    public function pilihHari(string $tanggal): void
    {
        $this->tanggal = $this->tanggalValid($tanggal);
    }

    private function tanggalValid(?string $tanggal): string
    {
        try {
            return Carbon::parse($tanggal ?: now())->toDateString();
        } catch (\Throwable) {
            return now()->toDateString();
        }
    }

    public function render(JadwalService $service)
    {
        $guruId = Auth::user()->guru_id;
        $dipilih = Carbon::parse($this->tanggal);
        $awalMinggu = $dipilih->copy()->startOfWeek(Carbon::MONDAY);
        $ringkasan = $guruId ? $service->ringkasanMingguGuru($guruId) : [];

        $hari = collect(range(0, 5))
            ->map(fn ($i) => $awalMinggu->copy()->addDays($i))
            ->filter(fn ($d) => $d->dayOfWeekIso <= 5 || ($ringkasan[Hari::fromIso($d->dayOfWeekIso)?->value] ?? 0) > 0)
            ->map(fn ($d) => [
                'tanggal' => $d->toDateString(),
                'singkat' => $d->locale('id')->isoFormat('ddd'),
                'tgl' => $d->day,
                'jam' => $ringkasan[Hari::fromIso($d->dayOfWeekIso)?->value] ?? 0,
                'hari_ini' => $d->isToday(),
                'dipilih' => $d->isSameDay($dipilih),
            ])
            ->values();

        return view('livewire.mobile.guru.jadwal-guru', [
            'hari' => $hari,
            'dipilih' => $dipilih,
            'data' => $guruId ? $service->jadwalGuruHarian($guruId, $dipilih) : ['hari_kerja' => false, 'items' => [], 'jumlah_blok' => 0, 'belum_jurnal' => 0],
            'mingguLalu' => $awalMinggu->copy()->subWeek()->toDateString(),
            'mingguDepan' => $awalMinggu->copy()->addWeek()->toDateString(),
        ])->layout('components.layouts.pwa', ['judul' => 'Jadwal Mengajar']);
    }
}
