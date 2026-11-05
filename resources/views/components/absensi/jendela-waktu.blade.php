{{--
    Garis waktu jam absen hari ini, dengan penanda "Sekarang".
    Dipakai di layar absen guru & tendik.

    @props
      jendela : array dari AturanAbsensiService::jendela()
      sekarang: "HH:MM"
      tone    : 'dark' (di atas navy) | 'light' (di atas ivory/putih)
--}}
@props(['jendela', 'sekarang', 'tone' => 'dark'])

@php
    $titik = [
        ['jam' => $jendela['masuk_buka'], 'label' => 'Absen masuk dibuka'],
        ['jam' => $jendela['batas_terlambat'], 'label' => 'Batas tepat waktu', 'catatan' => $jendela['toleransi_menit'] > 0 ? "{$jendela['masuk_tepat']} + toleransi {$jendela['toleransi_menit']} mnt" : null],
        ['jam' => $jendela['masuk_tutup'], 'label' => 'Absen masuk ditutup'],
        ['jam' => $jendela['pulang_buka'], 'label' => 'Jam pulang', 'catatan' => $jendela['pulang_cepat_diizinkan'] ? 'Sebelumnya: pulang cepat + alasan' : null],
        ['jam' => $jendela['pulang_tutup'], 'label' => 'Absen pulang ditutup'],
    ];

    // Sisipkan penanda "Sekarang" sesuai urutan waktu
    $posisi = collect($titik)->search(fn ($t) => $t['jam'] > $sekarang);
    $posisi = $posisi === false ? count($titik) : $posisi;
    array_splice($titik, $posisi, 0, [['jam' => $sekarang, 'label' => 'Sekarang', 'sekarang' => true]]);

    $dark = $tone === 'dark';
@endphp

<ol {{ $attributes->class(['relative space-y-0']) }} aria-label="Jam absen hari ini">
    @foreach ($titik as $i => $t)
        @php
            $isNow = $t['sekarang'] ?? false;
            $lewat = ! $isNow && $t['jam'] <= $sekarang;
            $terakhir = $loop->last;
        @endphp
        <li class="relative flex gap-3 {{ $terakhir ? '' : 'pb-3.5' }}">
            {{-- Garis penghubung --}}
            @unless ($terakhir)
                <span aria-hidden="true" class="absolute left-[0.4375rem] top-4 -bottom-0.5 w-px {{ $dark ? 'bg-white/15' : 'bg-navy-200' }}"></span>
            @endunless

            {{-- Titik --}}
            <span aria-hidden="true" class="relative mt-1 flex h-3.5 w-3.5 shrink-0 items-center justify-center">
                @if ($isNow)
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-gold-400/60 motion-reduce:animate-none"></span>
                    <span class="relative h-3.5 w-3.5 rounded-full bg-gold-400 ring-4 {{ $dark ? 'ring-gold-400/20' : 'ring-gold-200' }}"></span>
                @else
                    <span class="h-2.5 w-2.5 rounded-full {{ $lewat ? ($dark ? 'bg-white/30' : 'bg-navy-200') : ($dark ? 'border-2 border-white/50' : 'border-2 border-navy-400') }}"></span>
                @endif
            </span>

            {{-- Isi --}}
            <div class="flex min-w-0 flex-1 items-baseline justify-between gap-3">
                <div class="min-w-0">
                    <p @class([
                        'text-sm leading-tight',
                        'font-semibold text-gold-300' => $isNow && $dark,
                        'font-semibold text-gold-700' => $isNow && ! $dark,
                        'text-white/45' => $lewat && $dark,
                        'text-navy-300' => $lewat && ! $dark,
                        'text-white/90' => ! $isNow && ! $lewat && $dark,
                        'text-navy-800' => ! $isNow && ! $lewat && ! $dark,
                    ])>{{ $t['label'] }}</p>
                    @if (! empty($t['catatan']))
                        <p class="mt-0.5 text-[11px] leading-snug {{ $dark ? 'text-white/40' : 'text-navy-400' }}">{{ $t['catatan'] }}</p>
                    @endif
                </div>
                <time @class([
                    'shrink-0 font-heading text-sm tabular-nums',
                    'font-semibold text-gold-300' => $isNow && $dark,
                    'font-semibold text-gold-700' => $isNow && ! $dark,
                    'text-white/45' => $lewat && $dark,
                    'text-navy-300' => $lewat && ! $dark,
                    'font-medium text-white' => ! $isNow && ! $lewat && $dark,
                    'font-medium text-navy-900' => ! $isNow && ! $lewat && ! $dark,
                ])>{{ $t['jam'] }}</time>
            </div>
        </li>
    @endforeach
</ol>
