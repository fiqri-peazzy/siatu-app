{{-- Navigasi bulan ringkas untuk mobile (pengganti <select> bawaan). Komponen Livewire wajib punya geserBulan(int $arah). --}}
@props(['bulan', 'tahun'])
@php
    $tgl = \Carbon\Carbon::create((int) $tahun, (int) $bulan, 1);
    $bulanIni = $tgl->isSameMonth(now());
@endphp
<div {{ $attributes->class('inline-flex h-10 items-center rounded-full bg-white ring-1 ring-ivory-300') }} role="group" aria-label="Pilih bulan">
    <button type="button" wire:click="geserBulan(-1)" class="flex h-10 w-9 items-center justify-center rounded-l-full text-navy-500 hover:text-navy-900" aria-label="Bulan sebelumnya">
        <x-ui.icon name="chevron-kanan" class="h-4 w-4 rotate-180" />
    </button>
    <span class="min-w-[7.5rem] text-center text-[13px] font-semibold text-navy-800" aria-live="polite">{{ $tgl->locale('id')->isoFormat('MMMM YYYY') }}</span>
    <button type="button" wire:click="geserBulan(1)" @disabled($bulanIni) class="flex h-10 w-9 items-center justify-center rounded-r-full text-navy-500 hover:text-navy-900 disabled:text-navy-200" aria-label="Bulan berikutnya">
        <x-ui.icon name="chevron-kanan" class="h-4 w-4" />
    </button>
</div>
