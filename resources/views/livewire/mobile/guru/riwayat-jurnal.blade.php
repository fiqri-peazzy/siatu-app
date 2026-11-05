@php
    $statWarna = ['hadir' => 'text-ok-500', 'izin' => 'text-info-500', 'sakit' => 'text-warn-500', 'alfa' => 'text-bad-500'];
    $perTanggal = $jurnals->getCollection()->groupBy(fn ($j) => $j->tanggal->toDateString());
    $urlIsi = route('guru.jurnal.form', ['sesi' => '__SESI__']);
@endphp

<div class="font-body">
    {{-- Judul + unduh --}}
    <div class="flex items-end justify-between gap-3">
        <div>
            <p class="text-[12px] font-medium text-navy-400">Jurnal mengajar</p>
            <h1 class="font-heading text-[1.35rem] font-semibold leading-tight text-navy-900">Riwayat</h1>
        </div>
        <a href="{{ route('guru.jurnal.cetak', ['bulan' => $filterBulan, 'tahun' => $filterTahun]) }}" target="_blank"
           class="inline-flex h-10 items-center gap-1.5 rounded-full bg-white px-3.5 text-[13px] font-semibold text-navy-800 ring-1 ring-ivory-300 active:scale-95">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            PDF
        </a>
    </div>

    {{-- Filter --}}
    <div class="mt-4 grid grid-cols-2 gap-2">
        <x-ui.pilih-bulan :bulan="$filterBulan" :tahun="$filterTahun" class="w-full justify-between sm:w-auto" />
        <label class="relative">
            <span class="sr-only">Rombel</span>
            <select wire:model.live="filterKelas" class="h-11 w-full appearance-none rounded-2xl border-0 bg-white pl-4 pr-9 text-[14px] font-semibold text-navy-800 ring-1 ring-ivory-300 focus:ring-2 focus:ring-gold-500">
                <option value="">Semua rombel</option>
                @foreach ($kelasList as $kelas)
                    <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                @endforeach
            </select>
            <x-ui.icon name="chevron-kanan" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 rotate-90 text-navy-400" />
        </label>
    </div>

    {{-- Ringkasan bulan --}}
    <div class="mt-3 grid grid-cols-2 gap-2" wire:loading.class="opacity-50">
        <div class="rounded-2xl bg-navy-900 p-4 text-white">
            <p class="text-[12px] text-white/60">Pertemuan</p>
            <p class="mt-0.5 font-heading text-2xl font-semibold">{{ $ringkasan['pertemuan'] }}</p>
        </div>
        <div class="rounded-2xl bg-white p-4 ring-1 ring-ivory-300">
            <p class="text-[12px] text-navy-400">Jam pelajaran</p>
            <p class="mt-0.5 font-heading text-2xl font-semibold text-navy-900">{{ $ringkasan['jp'] }} <span class="text-sm font-medium text-navy-300">JP</span></p>
        </div>
    </div>

    {{-- Daftar per tanggal --}}
    <div class="mt-6 space-y-6" wire:loading.class="opacity-50">
        @forelse ($perTanggal as $tanggal => $daftar)
            @php $tgl = \Carbon\Carbon::parse($tanggal); @endphp
            <section>
                <h2 class="mb-2 flex items-baseline gap-2 text-[13px] font-semibold text-navy-500">
                    <span class="font-heading text-[15px] text-navy-900">{{ $tgl->isToday() ? 'Hari ini' : ($tgl->isYesterday() ? 'Kemarin' : $tgl->locale('id')->isoFormat('dddd')) }}</span>
                    {{ $tgl->locale('id')->isoFormat('D MMMM YYYY') }}
                </h2>
                <div class="space-y-2.5">
                    @foreach ($daftar as $j)
                        <button type="button" wire:click="showDetail({{ json_encode($j->jurnal_ids) }})"
                                class="w-full rounded-2xl bg-white p-4 text-left ring-1 ring-ivory-300 transition active:scale-[0.99]">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="truncate font-heading text-[15px] font-semibold text-navy-900">{{ $j->mapel->nama ?? '-' }}</h3>
                                    <p class="text-[13px] text-navy-500">{{ $j->kelas->nama ?? '-' }}</p>
                                </div>
                                <span class="shrink-0 rounded-lg bg-ivory-200 px-2 py-1 text-[11px] font-semibold tabular-nums text-navy-500">
                                    Jam {{ $j->jam_mulai }}{{ $j->jam_mulai == $j->jam_selesai ? '' : '–'.$j->jam_selesai }}
                                </span>
                            </div>
                            <p class="mt-2 line-clamp-2 text-[14px] leading-relaxed text-navy-700">{{ $j->materi }}</p>
                            <div class="mt-3 flex items-center gap-3 text-[12px] font-semibold tabular-nums">
                                @foreach ($j->stat as $kode => $n)
                                    <span class="{{ $n > 0 || $kode === 'hadir' ? $statWarna[$kode] : 'text-navy-200' }}">{{ $n }} {{ ucfirst($kode) }}</span>
                                @endforeach
                                @if (! empty($j->dokumentasi))
                                    <span class="ml-auto inline-flex items-center gap-1 text-navy-400"><x-ui.icon name="kamera" class="h-3.5 w-3.5" />{{ count($j->dokumentasi) }}</span>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="rounded-3xl border border-dashed border-ivory-400 bg-ivory-50 px-6 py-12 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-navy-300 ring-1 ring-ivory-300"><x-ui.icon name="jurnal" /></span>
                <h3 class="mt-3 font-heading text-[15px] font-semibold text-navy-800">Belum ada jurnal</h3>
                <p class="mt-1 text-[13px] text-navy-400">Jurnal yang Anda isi bulan ini akan muncul di sini.</p>
                <a wire:navigate href="{{ route('guru.jadwal') }}" class="mt-4 inline-flex h-10 items-center rounded-full bg-navy-900 px-4 text-[13px] font-semibold text-white">Buka jadwal hari ini</a>
            </div>
        @endforelse
    </div>

    @if ($jurnals->hasPages())
        <div class="mt-6">{{ $jurnals->links('pagination::simple-tailwind') }}</div>
    @endif

    {{-- Detail (bottom sheet) --}}
    @if ($showModal && $selectedJurnals)
        @php
            $first = $selectedJurnals->first();
            $sesiIds = $selectedJurnals->pluck('jadwal_sesi_id')->filter()->unique()->implode(',');
            $foto = collect($first->dokumentasi ?? []);
        @endphp
        <div class="fixed inset-0 z-50 flex items-end justify-center bg-navy-950/60 backdrop-blur-[2px] sm:items-center sm:p-6" wire:click.self="closeModal" @keydown.escape.window="$wire.closeModal()">
            <div role="dialog" aria-modal="true" aria-labelledby="judul-detail-jurnal"
                 class="flex max-h-[88dvh] w-full max-w-md flex-col rounded-t-[1.75rem] bg-ivory-50 font-body shadow-sheet sm:rounded-[1.75rem]">
                <div class="flex justify-center pt-3 sm:hidden"><span class="h-1.5 w-10 rounded-full bg-navy-200"></span></div>
                <div class="overflow-y-auto px-5 pt-3 pb-4">
                    <p class="text-[12px] text-navy-400">{{ $first->tanggal->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · Jam ke-{{ $selectedJurnals->pluck('jam_ke')->implode(', ') }}</p>
                    <h2 id="judul-detail-jurnal" class="mt-1 font-heading text-xl font-semibold text-navy-900">{{ $first->mapel->nama ?? '-' }}</h2>
                    <p class="text-[13px] text-navy-500">{{ $first->kelas->nama ?? '-' }}</p>

                    <div class="mt-4 grid grid-cols-4 gap-2 text-center">
                        @foreach (['hadir' => $first->jumlah_hadir, 'izin' => $first->jumlah_izin, 'sakit' => $first->jumlah_sakit, 'alfa' => $first->jumlah_alfa] as $kode => $n)
                            <div class="rounded-xl bg-white py-2 ring-1 ring-ivory-300"><p class="font-heading text-lg font-semibold {{ $statWarna[$kode] }}">{{ (int) $n }}</p><p class="text-[11px] text-navy-400">{{ ucfirst($kode) }}</p></div>
                        @endforeach
                    </div>

                    <dl class="mt-4 space-y-3 text-[14px]">
                        @if ($first->kd_cp)
                            <div><dt class="text-[12px] font-semibold text-navy-400">KD / CP</dt><dd class="mt-0.5 text-navy-900">{{ $first->kd_cp }}</dd></div>
                        @endif
                        <div><dt class="text-[12px] font-semibold text-navy-400">Materi</dt><dd class="mt-0.5 text-navy-900">{{ $detailMateri ?: '—' }}</dd></div>
                        <div><dt class="text-[12px] font-semibold text-navy-400">Kegiatan</dt><dd class="mt-0.5 text-navy-800">{{ $detailAktivitas ?: '—' }}</dd></div>
                        @if ($detailCatatan)
                            <div class="rounded-xl bg-warn-50 p-3"><dt class="text-[12px] font-semibold text-warn-500">Catatan supervisi</dt><dd class="mt-0.5 text-navy-800">{{ $detailCatatan }}</dd></div>
                        @endif
                    </dl>

                    @if ($foto->isNotEmpty())
                        <div class="mt-4 grid grid-cols-3 gap-2">
                            @foreach ($foto as $path)
                                <a href="{{ Storage::disk('r2')->url($path) }}" target="_blank" class="aspect-square overflow-hidden rounded-xl ring-1 ring-ivory-300">
                                    <img src="{{ Storage::disk('r2')->url($path) }}" alt="Dokumentasi" loading="lazy" class="h-full w-full object-cover">
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="grid gap-2.5 border-t border-ivory-300 px-5 pt-4 pb-safe-lg {{ $sesiIds ? 'grid-cols-2' : '' }}">
                    @if ($sesiIds)
                        <a wire:navigate href="{{ str_replace('__SESI__', $sesiIds, $urlIsi) }}?tgl={{ $first->tanggal->toDateString() }}" class="flex h-12 items-center justify-center rounded-2xl bg-navy-900 text-[15px] font-semibold text-white">Ubah jurnal</a>
                    @endif
                    <button type="button" wire:click="closeModal" class="h-12 rounded-2xl text-[15px] font-semibold text-navy-700 ring-1 ring-ivory-400">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>
