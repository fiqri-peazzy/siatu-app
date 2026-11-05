{{-- Tombol notifikasi + badge belum dibaca. Variabel: $unreadCount, $isGuru --}}
@php $adaNotifikasi = $isGuru && Route::has('guru.notifikasi'); @endphp

@if ($adaNotifikasi)
    <a wire:navigate href="{{ route('guru.notifikasi') }}"
       aria-label="Notifikasi{{ $unreadCount ? " ({$unreadCount} belum dibaca)" : '' }}"
       class="relative flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-white/80 transition hover:bg-white/10 hover:text-white">
        <x-ui.icon name="lonceng" class="h-6 w-6" />
        @if ($unreadCount > 0)
            <span class="absolute right-1.5 top-1.5 flex h-[1.125rem] min-w-[1.125rem] items-center justify-center rounded-full bg-gold-400 px-1 text-[10px] font-bold leading-none text-navy-950 ring-2 ring-navy-900">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </a>
@endif
