@php
    $label = ['hadir' => 'Tepat waktu', 'terlambat' => 'Terlambat', 'pulang_cepat' => 'Pulang cepat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alfa' => 'Alfa', 'dinas_luar' => 'Dinas luar'];
    $warna = ['hadir' => 'bg-ok-50 text-ok-500', 'terlambat' => 'bg-warn-50 text-warn-500', 'pulang_cepat' => 'bg-warn-50 text-warn-500', 'alfa' => 'bg-bad-50 text-bad-500'];
    $hari = collect($riwayat);
    $ringkas = [
        'Hadir' => $hari->filter(fn ($r) => $r['masuk'])->count(),
        'Terlambat' => $hari->filter(fn ($r) => $r['masuk']?->status === 'terlambat')->count(),
        'Tanpa pulang' => $hari->filter(fn ($r) => $r['masuk'] && ! $r['pulang'] && ! \Carbon\Carbon::parse($r['tanggal'])->isToday())->count(),
    ];
@endphp

<div class="font-body">
    <div class="flex items-end justify-between gap-3">
        <div>
            <p class="text-[12px] font-medium text-navy-400">Absensi saya</p>
            <h1 class="font-heading text-[1.35rem] font-semibold leading-tight text-navy-900">Riwayat</h1>
        </div>
        <label class="relative">
            <span class="sr-only">Bulan</span>
            <select wire:model.live="filterBulan" class="h-10 appearance-none rounded-full border-0 bg-white pl-4 pr-9 text-[13px] font-semibold text-navy-800 ring-1 ring-ivory-300 focus:ring-2 focus:ring-gold-500">
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ \Carbon\Carbon::create()->month($i)->locale('id')->isoFormat('MMMM') }}</option>
                @endfor
            </select>
            <x-ui.icon name="chevron-kanan" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 rotate-90 text-navy-400" />
        </label>
    </div>

    <div class="mt-4 grid grid-cols-3 gap-2" wire:loading.class="opacity-50">
        @foreach ($ringkas as $judul => $n)
            <div class="rounded-2xl bg-white px-3 py-3 ring-1 ring-ivory-300">
                <p class="font-heading text-xl font-semibold text-navy-900">{{ $n }}</p>
                <p class="text-[11px] text-navy-400">{{ $judul }}</p>
            </div>
        @endforeach
    </div>

    <ul class="mt-5 space-y-2.5" wire:loading.class="opacity-50">
        @forelse ($riwayat as $r)
            @php $tgl = \Carbon\Carbon::parse($r['tanggal']); @endphp
            <li class="flex gap-3.5 rounded-2xl bg-white p-4 ring-1 ring-ivory-300">
                <div class="w-11 shrink-0 text-center">
                    <p class="text-[11px] font-medium text-navy-400">{{ $tgl->locale('id')->isoFormat('ddd') }}</p>
                    <p class="font-heading text-xl font-semibold leading-tight text-navy-900">{{ $tgl->day }}</p>
                </div>
                <div class="grid min-w-0 flex-1 grid-cols-2 gap-3 border-l border-ivory-300 pl-3.5">
                    @foreach (['masuk' => 'Masuk', 'pulang' => 'Pulang'] as $jenis => $judul)
                        @php $a = $r[$jenis]; @endphp
                        <div class="min-w-0">
                            <p class="text-[11px] text-navy-400">{{ $judul }}</p>
                            @if ($a)
                                <p class="font-heading text-[15px] font-semibold tabular-nums text-navy-900">
                                    {{ \Carbon\Carbon::parse($a->waktu_absen)->format('H:i') }}
                                    @if ($a->foto_path)
                                        <a href="{{ Storage::disk('r2')->url($a->foto_path) }}" target="_blank" class="ml-1 inline-flex align-middle text-navy-300 hover:text-navy-600" aria-label="Lihat swafoto {{ strtolower($judul) }}"><x-ui.icon name="kamera" class="h-3.5 w-3.5" /></a>
                                    @endif
                                </p>
                                <span class="mt-0.5 inline-block rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $warna[$a->status] ?? 'bg-ivory-200 text-navy-600' }}">{{ $label[$a->status] ?? ucfirst($a->status) }}</span>
                            @else
                                <p class="font-heading text-[15px] font-semibold text-navy-200">--:--</p>
                                <span class="text-[11px] {{ $jenis === 'pulang' && $r['masuk'] && ! $tgl->isToday() ? 'text-bad-500' : 'text-navy-300' }}">{{ $jenis === 'pulang' && $r['masuk'] && ! $tgl->isToday() ? 'Tidak absen pulang' : 'Belum' }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </li>
        @empty
            <li class="rounded-3xl border border-dashed border-ivory-400 bg-ivory-50 px-6 py-12 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-navy-300 ring-1 ring-ivory-300"><x-ui.icon name="riwayat" /></span>
                <h3 class="mt-3 font-heading text-[15px] font-semibold text-navy-800">Belum ada absensi</h3>
                <p class="mt-1 text-[13px] text-navy-400">Riwayat absen masuk & pulang bulan ini akan tampil di sini.</p>
            </li>
        @endforelse
    </ul>
</div>
