@php
    $sekarang = now();
    $jam = (int) $sekarang->format('H');
    $sapaan = match (true) { $jam < 11 => 'Selamat pagi', $jam < 15 => 'Selamat siang', $jam < 18 => 'Selamat sore', default => 'Selamat malam' };
    $namaDepan = \Illuminate\Support\Str::of($tendik->nama ?? auth()->user()->name)->explode(' ')->first();
@endphp

<div class="pb-6 font-body">
    {{-- Hero --}}
    <section class="relative overflow-hidden rounded-b-[2rem] bg-console px-5 pt-[max(env(safe-area-inset-top),1rem)] pb-20 text-white">
        <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="" aria-hidden="true" class="pointer-events-none absolute -right-10 -bottom-12 h-56 w-56 object-contain opacity-[0.07]">
        <div class="relative flex h-14 items-center gap-2.5">
            <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="" class="h-8 w-8 object-contain">
            <span class="font-heading text-[15px] font-semibold tracking-tight">SIATU <span class="text-white/50">·</span> <span class="text-gold-300">Tendik</span></span>
        </div>
        <div class="relative mt-4">
            <p class="text-sm text-white/65">{{ $sapaan }},</p>
            <h1 class="mt-0.5 font-heading text-[1.7rem] font-semibold leading-tight">{{ $namaDepan }}</h1>
            <p class="mt-1.5 text-[13px] text-white/55">{{ $sekarang->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
    </section>

    {{-- Kartu absen --}}
    <section class="relative z-10 -mt-14 px-4">
        <div class="rounded-3xl bg-white p-4 shadow-float ring-1 ring-ivory-300">
            <div class="grid grid-cols-2 divide-x divide-ivory-300">
                @foreach ([['Masuk', $absenMasuk], ['Pulang', $absenPulang]] as [$label, $absen])
                    <div class="{{ $loop->first ? 'pr-4' : 'pl-4' }}">
                        <p class="text-[12px] font-medium text-navy-400">Absen {{ strtolower($label) }}</p>
                        @if ($absen)
                            <p class="mt-1 font-heading text-2xl font-semibold tabular-nums text-navy-900">{{ \Carbon\Carbon::parse($absen->waktu_absen)->format('H:i') }}</p>
                            <span class="mt-1 inline-block rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $absen->status === 'hadir' ? 'bg-ok-50 text-ok-500' : 'bg-warn-50 text-warn-500' }}">{{ ucwords(str_replace('_', ' ', $absen->status)) }}</span>
                        @else
                            <p class="mt-1 font-heading text-2xl font-semibold text-navy-200">--:--</p>
                            <span class="mt-1 inline-block text-[11px] text-navy-400">Belum tercatat</span>
                        @endif
                    </div>
                @endforeach
            </div>
            @if (! ($absenMasuk && $absenPulang))
                <a wire:navigate href="{{ route('akademik.absensi.tendik.form') }}"
                   class="mt-4 flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-navy-900 text-[15px] font-semibold text-white transition hover:bg-navy-800 active:scale-[0.98]">
                    <x-ui.icon name="absen" class="h-5 w-5 text-gold-300" />
                    {{ $absenMasuk ? 'Absen pulang' : 'Absen masuk sekarang' }}
                </a>
            @else
                <p class="mt-4 flex items-center justify-center gap-2 rounded-2xl bg-ok-50 py-3 text-[14px] font-semibold text-ok-500"><x-ui.icon name="cek" class="h-4 w-4" /> Absen hari ini lengkap</p>
            @endif
        </div>
    </section>

    {{-- Ringkasan bulan --}}
    <section class="mt-6 grid grid-cols-2 gap-3 px-4">
        <div class="rounded-2xl bg-white p-4 ring-1 ring-ivory-300">
            <p class="text-[12px] font-medium text-navy-400">Hadir bulan ini</p>
            <p class="mt-1 font-heading text-2xl font-semibold text-navy-900">{{ $ringkasan['hadir'] }} <span class="text-sm font-medium text-navy-300">hari</span></p>
        </div>
        <div class="rounded-2xl bg-white p-4 ring-1 ring-ivory-300">
            <p class="text-[12px] font-medium text-navy-400">Terlambat</p>
            <p class="mt-1 font-heading text-2xl font-semibold {{ $ringkasan['terlambat'] ? 'text-warn-500' : 'text-navy-900' }}">{{ $ringkasan['terlambat'] }} <span class="text-sm font-medium text-navy-300">kali</span></p>
        </div>
    </section>

    {{-- Jam absen hari ini --}}
    <section class="mt-6 px-4">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-heading text-[17px] font-semibold text-navy-900">Jam absen hari ini</h2>
            <a wire:navigate href="{{ route('akademik.absensi.tendik.riwayat') }}" class="text-[13px] font-semibold text-navy-600">Riwayat</a>
        </div>
        @if ($jendela['hari_kerja'] && ! $jendela['libur'])
            <div class="rounded-2xl bg-white p-4 ring-1 ring-ivory-300">
                <x-absensi.jendela-waktu :jendela="$jendela" :sekarang="$sekarang->format('H:i')" tone="light" />
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-ivory-400 bg-ivory-50 px-6 py-8 text-center text-[13px] text-navy-500">
                {{ $jendela['libur'] ? 'Hari ini libur: '.$jendela['libur'] : 'Hari ini bukan hari kerja.' }}
            </div>
        @endif
    </section>
</div>
