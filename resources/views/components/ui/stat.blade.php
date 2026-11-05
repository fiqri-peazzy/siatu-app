{{-- Kartu statistik (quick info). tone: navy (sorotan gelap) | light --}}
@props(['label', 'nilai', 'satuan' => null, 'sub' => null, 'ikon' => 'info', 'tone' => 'light', 'persen' => null, 'href' => null])
@php $gelap = $tone === 'navy'; @endphp
<{{ $href ? 'a' : 'div' }} @if ($href) href="{{ $href }}" @endif
    {{ $attributes->class([
        'group relative flex flex-col overflow-hidden rounded-2xl p-4 transition sm:p-5',
        'bg-console text-white' => $gelap,
        'bg-white ring-1 ring-ivory-300' => ! $gelap,
        'hover:ring-navy-200' => $href && ! $gelap,
    ]) }}>
    <div class="flex items-start justify-between gap-3">
        <p class="text-[12px] font-semibold uppercase tracking-wider {{ $gelap ? 'text-white/55' : 'text-navy-400' }}">{{ $label }}</p>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $gelap ? 'bg-white/10 text-gold-300' : 'bg-ivory-200 text-navy-600' }}">
            <x-ui.icon :name="$ikon" class="h-5 w-5" />
        </span>
    </div>
    <p class="mt-2 font-heading text-[1.75rem] font-semibold leading-none tabular-nums {{ $gelap ? 'text-white' : 'text-navy-900' }}">
        {{ $nilai }}@if ($satuan)<span class="ml-1 text-base font-medium {{ $gelap ? 'text-white/50' : 'text-navy-300' }}">{{ $satuan }}</span>@endif
    </p>
    @if ($persen !== null)
        <div class="mt-3 h-1.5 overflow-hidden rounded-full {{ $gelap ? 'bg-white/10' : 'bg-ivory-300' }}">
            <div class="h-full rounded-full {{ $persen >= 85 ? 'bg-ok-400' : ($persen >= 60 ? 'bg-gold-400' : 'bg-bad-400') }}" style="width: {{ min(100, max(0, $persen)) }}%"></div>
        </div>
    @endif
    @if ($sub)
        <p class="mt-2 text-[12px] leading-snug {{ $gelap ? 'text-white/60' : 'text-navy-500' }}">{!! $sub !!}</p>
    @endif
</{{ $href ? 'a' : 'div' }}>
