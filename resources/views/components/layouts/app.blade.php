<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ isset($header) && trim(strip_tags($header)) ? trim(strip_tags($header)).' · ' : '' }}SIATU SMAMGo</title>

        <link rel="icon" type="image/png" href="{{ asset('images/logo-removebg-preview.png') }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#0b1d3a">
        <link rel="apple-touch-icon" href="{{ asset('images/logo-removebg-preview.png') }}">

        @include('components.layouts.partials.fonts')
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13"></script>

        <style>
            @media (min-width: 1024px) {
                .admin-sidebar.collapsed { width: 4.75rem; }
                .admin-sidebar.collapsed .sidebar-label { display: none; }
            }
        </style>
    </head>
    @php
        $u = auth()->user();
        $ta = \App\Models\Master\MasterTahunAjaran::where('is_active', true)->first();
        $inisial = collect(explode(' ', trim($u->name ?? 'A')))->filter()->take(2)->map(fn ($k) => mb_substr($k, 0, 1))->implode('');
    @endphp
    <body class="bg-ivory-200 font-body text-navy-900 antialiased">
        <div class="flex h-dvh overflow-hidden"
             x-data="{ open: false, collapsed: (() => { try { return localStorage.getItem('sidebarCollapsed') === 'true' } catch (e) { return false } })() }"
             x-init="$watch('collapsed', v => { try { localStorage.setItem('sidebarCollapsed', v) } catch (e) {} })"
             @keydown.escape.window="open = false">

            {{-- Overlay drawer (mobile) --}}
            <div x-show="open" x-transition.opacity @click="open = false" class="fixed inset-0 z-40 bg-navy-950/60 backdrop-blur-[2px] lg:hidden" style="display:none"></div>

            {{-- Sidebar console --}}
            <aside class="admin-sidebar fixed inset-y-0 left-0 z-50 flex w-[17rem] -translate-x-full flex-col bg-console text-white transition-[transform,width] duration-300 lg:static lg:translate-x-0"
                   :class="{ 'translate-x-0': open, 'collapsed': collapsed }" aria-label="Sidebar">
                <div class="flex h-16 shrink-0 items-center gap-3 px-4">
                    <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="" class="h-9 w-9 shrink-0 object-contain">
                    <div class="sidebar-label min-w-0 leading-tight">
                        <p class="font-heading text-[15px] font-semibold">SIATU</p>
                        <p class="truncate text-[11px] text-white/50">SMA Muhammadiyah Kota Gorontalo</p>
                    </div>
                    <button type="button" @click="open = false" class="ml-auto flex h-9 w-9 items-center justify-center rounded-lg text-white/60 hover:bg-white/10 lg:hidden" aria-label="Tutup menu">
                        <x-ui.icon name="tutup" class="h-5 w-5" />
                    </button>
                </div>

                @include('components.layouts.partials.admin-menu')

                <div class="shrink-0 border-t border-white/10 p-3">
                    <div class="flex items-center gap-3 rounded-xl p-2">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gold-400 font-heading text-[13px] font-semibold text-navy-950">{{ $inisial }}</span>
                        <div class="sidebar-label min-w-0 flex-1 leading-tight">
                            <p class="truncate text-[13px] font-semibold">{{ $u->name ?? 'Pengguna' }}</p>
                            <p class="truncate text-[11px] text-white/50">{{ $u->role_label ?? '' }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="sidebar-label">
                            @csrf
                            <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-lg text-white/60 hover:bg-white/10 hover:text-white" aria-label="Keluar" title="Keluar">
                                <x-ui.icon name="keluar" class="h-5 w-5" />
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Konten --}}
            <div class="flex min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-3 border-b border-ivory-300 bg-ivory-50/85 px-4 backdrop-blur-md lg:px-8">
                    <button type="button" @click="open = true" class="-ml-1 flex h-10 w-10 items-center justify-center rounded-xl text-navy-700 hover:bg-ivory-200 lg:hidden" aria-label="Buka menu">
                        <x-ui.icon name="menu" class="h-6 w-6" />
                    </button>
                    <button type="button" @click="collapsed = ! collapsed" class="-ml-2 hidden h-10 w-10 items-center justify-center rounded-xl text-navy-600 hover:bg-ivory-200 lg:flex" :aria-label="collapsed ? 'Lebarkan menu' : 'Ciutkan menu'">
                        <x-ui.icon name="panel" class="h-5 w-5" />
                    </button>
                    <h1 class="min-w-0 truncate font-heading text-[17px] font-semibold text-navy-900 lg:text-lg">{{ $header ?? '' }}</h1>
                    <div class="ml-auto flex shrink-0 items-center gap-2">
                        <button type="button" @click="$dispatch('palette:buka')" aria-label="Cari halaman (Ctrl+K)"
                                class="flex h-10 items-center gap-2 rounded-xl bg-white px-3 text-[13px] text-navy-400 ring-1 ring-ivory-300 hover:ring-navy-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                            <span class="hidden lg:inline">Cari…</span>
                            <kbd class="hidden rounded-md bg-ivory-200 px-1.5 py-0.5 text-[11px] font-semibold text-navy-500 lg:inline">Ctrl K</kbd>
                        </button>
                        @if ($ta)
                            <span class="hidden rounded-full bg-white px-3 py-1.5 text-[12px] font-semibold text-navy-700 ring-1 ring-ivory-300 sm:inline-flex">TA {{ $ta->nama }} · {{ $ta->semester }}</span>
                        @endif
                        <span class="hidden rounded-full bg-navy-900 px-3 py-1.5 text-[12px] font-semibold text-white md:inline-flex">{{ now()->locale('id')->isoFormat('ddd, D MMM YYYY') }}</span>
                    </div>
                </header>

                <main id="konten" class="flex-1 overflow-y-auto overflow-x-hidden">
                    <div class="mx-auto w-full max-w-[1440px] px-4 py-5 sm:px-6 lg:px-8 lg:py-8">
                        @php
                            // Breadcrumb otomatis dari menu: Beranda › Grup › Halaman (› Tambah/Ubah)
                            $jejak = collect(require resource_path('views/components/layouts/partials/admin-menu-data.php'))
                                ->flatMap(fn ($items, $g) => collect($items)->map(fn ($i) => [$g, $i]))
                                ->first(fn ($x) => $x[1][2] && request()->routeIs($x[1][2]));
                            $aksi = str_ends_with((string) request()->route()?->getName(), '.create') ? 'Tambah'
                                : (str_ends_with((string) request()->route()?->getName(), '.edit') ? 'Ubah'
                                : (str_ends_with((string) request()->route()?->getName(), '.show') ? 'Detail' : null));
                        @endphp
                        @if ($jejak && $jejak[1][1] !== 'dashboard')
                            <nav aria-label="Breadcrumb" class="mb-4 flex flex-wrap items-center gap-1.5 text-[13px] text-navy-400">
                                <a href="{{ route('dashboard') }}" class="hover:text-navy-800">Beranda</a>
                                @if ($jejak[0]) <span aria-hidden="true">›</span> <span>{{ $jejak[0] }}</span> @endif
                                <span aria-hidden="true">›</span>
                                @if ($aksi)
                                    <a href="{{ route($jejak[1][1]) }}" class="hover:text-navy-800">{{ $jejak[1][0] }}</a>
                                    <span aria-hidden="true">›</span> <span class="font-semibold text-navy-800" aria-current="page">{{ $aksi }}</span>
                                @else
                                    <span class="font-semibold text-navy-800" aria-current="page">{{ $jejak[1][0] }}</span>
                                @endif
                            </nav>
                        @endif
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        @include('components.layouts.partials.admin-palette')

        @if (session()->has('success'))
            <script type="module">document.addEventListener('DOMContentLoaded', () => window.showAppAlert?.success(@js(session('success'))));</script>
        @endif
        @if (session()->has('error'))
            <script type="module">document.addEventListener('DOMContentLoaded', () => window.showAppAlert?.error(@js(session('error'))));</script>
        @endif

        <script>
            if ('serviceWorker' in navigator) window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js').catch(() => {}));

            // Tabel → kartu di layar kecil: beri label kolom ke setiap sel berdasarkan <thead>.
            // Tabel yang tidak ingin diubah: tambahkan atribut data-tetap-tabel.
            (function () {
                const tandai = () => document.querySelectorAll('#konten table:not([data-tetap-tabel])').forEach((t) => {
                    const judul = [...t.querySelectorAll('thead th')].map((th) => th.innerText.trim());
                    if (!judul.length) return;
                    t.classList.add('tabel-kartu');
                    t.querySelectorAll('tbody tr').forEach((tr) => [...tr.children].forEach((td, i) => {
                        if (!td.hasAttribute('data-label')) td.setAttribute('data-label', judul[i] ?? '');
                    }));
                });
                let jadwal = null;
                const pantau = () => {
                    tandai();
                    const konten = document.getElementById('konten');
                    if (konten) new MutationObserver(() => { cancelAnimationFrame(jadwal); jadwal = requestAnimationFrame(tandai); })
                        .observe(konten, { childList: true, subtree: true });
                };
                document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', pantau) : pantau();
            })();
        </script>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
