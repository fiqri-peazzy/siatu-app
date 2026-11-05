{{--
    Satu baris timeline jadwal guru (dari JadwalService::jadwalGuruHarian).
    @props item: array, bisaIsi: bool (hari ini) — tap blok membuka detail (event 'jadwal:detail')
--}}
@props(['item', 'bisaIsi' => false])

@php
    $tipe = $item['tipe'];
    $fase = $item['fase'] ?? 'mendatang';
@endphp

<li class="relative flex gap-3">
    {{-- Kolom waktu --}}
    <div class="w-12 shrink-0 pt-3 text-right">
        <p class="font-heading text-[13px] font-semibold tabular-nums {{ $fase === 'lewat' ? 'text-navy-300' : 'text-navy-900' }}">{{ $item['mulai'] }}</p>
        @if ($tipe === 'blok')
            <p class="text-[11px] tabular-nums text-navy-400">{{ $item['selesai'] }}</p>
        @endif
    </div>

    {{-- Rel + titik --}}
    <div class="relative flex w-3 shrink-0 justify-center" aria-hidden="true">
        <span class="absolute inset-y-0 w-px bg-ivory-400"></span>
        <span @class([
            'relative mt-4 h-3 w-3 rounded-full ring-4 ring-ivory-200',
            'bg-gold-400' => $fase === 'berlangsung',
            'bg-navy-800' => $tipe === 'blok' && $fase !== 'berlangsung',
            'bg-ivory-400' => $tipe !== 'blok' && $fase !== 'berlangsung',
        ])></span>
    </div>

    {{-- Isi --}}
    <div class="min-w-0 flex-1 pb-3">
        @if ($tipe === 'blok')
            @php
                $tergeser = $item['jurnal'] === 'tidak_wajib';
                $jp = count($item['jam_ke']);
            @endphp
            <button type="button"
                    @click="$dispatch('jadwal:detail', @js($item))"
                    @class([
                        'w-full rounded-2xl p-4 text-left ring-1 transition active:scale-[0.99]',
                        'bg-navy-900 text-white ring-navy-900 shadow-float' => $fase === 'berlangsung',
                        'bg-white ring-ivory-300' => $fase !== 'berlangsung' && ! $tergeser,
                        'bg-ivory-100 ring-ivory-300 opacity-70' => $tergeser,
                    ])>
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        @if ($fase === 'berlangsung')
                            <p class="mb-1.5 inline-flex items-center gap-1.5 text-[11px] font-semibold text-gold-300">
                                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-gold-300 motion-reduce:animate-none"></span> Sedang berlangsung
                            </p>
                        @endif
                        <h3 class="truncate font-heading text-[15px] font-semibold {{ $fase === 'berlangsung' ? 'text-white' : 'text-navy-900' }} {{ $tergeser ? 'line-through' : '' }}">{{ $item['mapel'] }}</h3>
                        <p class="mt-0.5 truncate text-[13px] {{ $fase === 'berlangsung' ? 'text-white/70' : 'text-navy-500' }}">
                            {{ implode(' + ', $item['kelas']) }} · {{ $jp }} JP
                        </p>
                    </div>
                    <span class="shrink-0 rounded-lg px-2 py-1 text-[11px] font-semibold tabular-nums {{ $fase === 'berlangsung' ? 'bg-white/10 text-white/80' : 'bg-ivory-200 text-navy-500' }}">
                        Jam {{ $item['jam_ke'][0] }}{{ $jp > 1 ? '–'.end($item['jam_ke']) : '' }}
                    </span>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-1.5">
                    @if ($item['lanjutan'] ?? false)
                        <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $fase === 'berlangsung' ? 'bg-white/10 text-white/80' : 'bg-ivory-200 text-navy-600' }}">Lanjutan setelah istirahat</span>
                    @endif
                    @if ($item['jenis'] === 'pilihan')
                        <span class="rounded-full bg-info-50 px-2 py-0.5 text-[11px] font-semibold text-info-500">Mapel pilihan</span>
                    @endif
                    @if (count($item['kelas']) > 1)
                        <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $fase === 'berlangsung' ? 'bg-white/10 text-white/80' : 'bg-ivory-200 text-navy-600' }}">Gabungan</span>
                    @endif
                    @switch($item['jurnal'])
                        @case('diisi')
                            <span class="inline-flex items-center gap-1 rounded-full bg-ok-50 px-2 py-0.5 text-[11px] font-semibold text-ok-500"><x-ui.icon name="cek" class="h-3 w-3" /> Jurnal terisi</span>
                            @break
                        @case('tidak_wajib')
                            <span class="rounded-full bg-ivory-200 px-2 py-0.5 text-[11px] font-semibold text-navy-500">{{ $item['catatan'] ?? 'Tergeser kegiatan' }}</span>
                            @break
                        @default
                            @if ($fase !== 'mendatang')
                                <span class="rounded-full bg-warn-50 px-2 py-0.5 text-[11px] font-semibold text-warn-500">{{ $item['jurnal'] === 'sebagian' ? 'Jurnal sebagian' : 'Jurnal belum diisi' }}</span>
                            @endif
                    @endswitch
                    @if (($item['status_slot'] ?? '') === 'terpotong')
                        <span class="rounded-full bg-warn-50 px-2 py-0.5 text-[11px] font-semibold text-warn-500">Waktu disesuaikan</span>
                    @endif
                </div>
            </button>
        @else
            <div class="flex min-h-11 items-center gap-2 rounded-xl px-3 py-2 text-[13px] {{ $tipe === 'kosong' ? 'text-navy-300' : 'bg-ivory-100 text-navy-600 ring-1 ring-ivory-300' }}">
                @if ($tipe === 'jeda')
                    <x-ui.icon :name="($item['jenis'] ?? '') === 'kegiatan' ? 'kegiatan' : 'jam'" class="h-4 w-4 text-navy-400" />
                @endif
                <span class="truncate">{{ $item['label'] }}</span>
                <span class="ml-auto shrink-0 tabular-nums text-navy-300">s/d {{ $item['selesai'] }}</span>
            </div>
        @endif
    </div>
</li>
