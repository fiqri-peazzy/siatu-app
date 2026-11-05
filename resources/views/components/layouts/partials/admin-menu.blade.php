{{-- Menu panel admin, didefinisikan sebagai data. Item tanpa route = fitur yang belum tersedia ("Segera"). --}}
@php
    $u = auth()->user();
    $grup = require resource_path('views/components/layouts/partials/admin-menu-data.php');
@endphp

<nav class="scroll-gelap flex-1 space-y-5 overflow-y-auto overflow-x-hidden px-3 py-4" aria-label="Menu utama">
    @foreach ($grup as $judul => $items)
        @php $items = array_filter($items, fn ($i) => ! $i[4] || $u?->hasAnyRole($i[4])); @endphp
        @continue(empty($items))
        <div>
            @if ($judul)
                <p class="sidebar-label mb-1.5 px-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-white/35">{{ $judul }}</p>
            @endif
            <ul class="space-y-0.5">
                @foreach ($items as [$label, $route, $pola, $ikon])
                    @php $aktif = $pola && request()->routeIs($pola); @endphp
                    <li>
                        @if ($route && Route::has($route))
                            <a href="{{ route($route) }}" @if ($aktif) aria-current="page" @endif title="{{ $label }}"
                               class="group relative flex h-10 items-center gap-3 rounded-xl px-3 text-[14px] font-medium transition {{ $aktif ? 'bg-white/10 text-white' : 'text-white/65 hover:bg-white/5 hover:text-white' }}">
                                @if ($aktif)<span class="absolute -left-3 top-2 bottom-2 w-1 rounded-r-full bg-gold-400"></span>@endif
                                <x-ui.icon :name="$ikon" class="h-5 w-5 {{ $aktif ? 'text-gold-300' : 'text-white/50 group-hover:text-white/80' }}" />
                                <span class="sidebar-label truncate">{{ $label }}</span>
                            </a>
                        @else
                            <span title="{{ $label }} — segera hadir" class="flex h-10 cursor-not-allowed items-center gap-3 rounded-xl px-3 text-[14px] font-medium text-white/30">
                                <x-ui.icon :name="$ikon" class="h-5 w-5" />
                                <span class="sidebar-label truncate">{{ $label }}</span>
                                <span class="sidebar-label ml-auto rounded-full bg-white/5 px-2 py-0.5 text-[10px] font-semibold">Segera</span>
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</nav>
