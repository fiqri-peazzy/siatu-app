<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        {{-- Zoom diizinkan (aksesibilitas); viewport-fit=cover untuk safe-area notch --}}
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $judul ?? 'Portal' }} · SIATU SMAMGo</title>

        <!-- PWA -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo-removebg-preview.png') }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#0b1d3a">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="SIATU">
        <link rel="apple-touch-icon" href="{{ asset('images/logo-removebg-preview.png') }}">

        @include('components.layouts.partials.fonts')
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { overscroll-behavior-y: none; -webkit-tap-highlight-color: transparent; }
        </style>
    </head>

    @php
        $user = auth()->user();
        $isGuru = $user?->hasRole('guru');
        $tanpaHeader = $tanpaHeader ?? false;
        $tanpaNav = $tanpaNav ?? false; // halaman dengan bar aksi sendiri (mis. form jurnal)
        $unreadCount = $user ? $user->unreadNotifications->count() : 0;
        $urlAbsen = $isGuru ? route('akademik.absensi.guru.form') : route('akademik.absensi.tendik.form');
    @endphp

    <body class="bg-ivory-200 font-body text-navy-900 antialiased">

        {{-- App bar (halaman selain Beranda; Beranda memakai hero sendiri) --}}
        @unless ($tanpaHeader)
            <header class="sticky top-0 z-40 bg-navy-900/95 pt-[env(safe-area-inset-top)] text-white backdrop-blur-md">
                <div class="mx-auto flex h-14 max-w-md items-center justify-between gap-3 px-4">
                    <a wire:navigate href="{{ $isGuru ? route('guru.dashboard') : route('tendik.beranda') }}" class="flex min-w-0 items-center gap-2.5">
                        <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="" class="h-8 w-8 shrink-0 object-contain">
                        <div class="min-w-0 leading-tight">
                            <p class="truncate font-heading text-[15px] font-semibold">{{ $judul ?? 'SIATU' }}</p>
                            <p class="text-[11px] text-white/55">{{ $isGuru ? 'Portal Guru' : 'Portal Tendik' }}</p>
                        </div>
                    </a>
                    @include('components.layouts.partials.pwa-lonceng', ['unreadCount' => $unreadCount, 'isGuru' => $isGuru])
                </div>
            </header>
        @endunless

        <main class="mx-auto min-h-dvh w-full max-w-md {{ $tanpaHeader ? '' : 'px-4 pt-4' }} {{ $tanpaNav ? 'pb-4' : 'pb-[calc(6rem+env(safe-area-inset-bottom))]' }}">
            {{ $slot }}
        </main>

        {{-- Bottom navigation: tombol Absen menonjol di tengah --}}
        @unless ($tanpaNav)
        <nav id="bottom-nav" aria-label="Navigasi utama"
             class="fixed inset-x-0 bottom-0 z-40 border-t border-ivory-300 bg-ivory-50/95 pb-[env(safe-area-inset-bottom)] backdrop-blur-md">
            <div class="mx-auto grid h-16 max-w-md {{ $isGuru ? 'grid-cols-5' : 'grid-cols-3' }} items-stretch px-1">
                @php
                    $tabs = $isGuru
                        ? [
                            ['Beranda', route('guru.dashboard'), request()->routeIs('guru.dashboard'), 'dashboard'],
                            ['Jadwal', route('guru.jadwal'), request()->routeIs('guru.jadwal'), 'jadwal'],
                            null, // tombol Absen
                            ['Jurnal', route('guru.jurnal.riwayat'), request()->routeIs('guru.jurnal.*'), 'jurnal'],
                            ['Profil', route('guru.profil'), request()->routeIs('guru.profil', 'guru.pengaturan-notifikasi', 'akademik.absensi.guru.riwayat'), 'pengguna'],
                        ]
                        : [
                            ['Beranda', route('tendik.beranda'), request()->routeIs('tendik.beranda'), 'dashboard'],
                            null,
                            ['Riwayat', route('akademik.absensi.tendik.riwayat'), request()->routeIs('akademik.absensi.tendik.riwayat'), 'riwayat'],
                        ];
                    $absenAktif = request()->routeIs('akademik.absensi.guru.form', 'akademik.absensi.tendik.form');
                @endphp

                @foreach ($tabs as $tab)
                    @if ($tab === null)
                        <div class="relative flex justify-center">
                            <a wire:navigate href="{{ $urlAbsen }}" aria-label="Absen sekarang"
                               class="absolute -top-5 flex h-[3.75rem] w-[3.75rem] flex-col items-center justify-center rounded-full bg-gold-400 text-navy-950 shadow-[0_10px_24px_-6px_rgb(184_147_42/0.6)] ring-4 ring-ivory-50 transition active:scale-95 {{ $absenAktif ? 'bg-gold-300' : '' }}">
                                <x-ui.icon name="absen" class="h-6 w-6" />
                            </a>
                            <span class="mt-auto pb-2 text-[11px] font-semibold {{ $absenAktif ? 'text-navy-900' : 'text-navy-500' }}">Absen</span>
                        </div>
                    @else
                        @php [$label, $href, $aktif, $ikon] = $tab; @endphp
                        <a wire:navigate href="{{ $href }}" @if ($aktif) aria-current="page" @endif
                           class="flex flex-col items-center justify-center gap-1 text-[11px] font-semibold transition {{ $aktif ? 'text-navy-900' : 'text-navy-400 hover:text-navy-700' }}">
                            <span class="flex h-7 w-12 items-center justify-center rounded-full transition {{ $aktif ? 'bg-navy-900 text-gold-300' : '' }}">
                                <x-ui.icon :name="$ikon" class="h-5 w-5" />
                            </span>
                            {{ $label }}
                        </a>
                    @endif
                @endforeach
            </div>
        </nav>
        @endunless

        @include('components.layouts.partials.pwa-onboarding', ['isGuru' => $isGuru, 'user' => $user])

        <script>
            function urlBase64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                const raw = window.atob(base64);
                return Uint8Array.from([...raw].map((c) => c.charCodeAt(0)));
            }

            // Langganan push. Izin TIDAK diminta otomatis — diminta di onboarding (gestur pengguna).
            window.siatuSubscribePush = async function () {
                const key = @js(config('webpush.vapid.public_key'));
                if (!key || !('serviceWorker' in navigator) || !('PushManager' in window)) return false;
                const reg = await navigator.serviceWorker.ready;
                const sub = (await reg.pushManager.getSubscription())
                    || await reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: urlBase64ToUint8Array(key) });
                await fetch('/webpush/subscribe', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(sub),
                });
                return true;
            };

            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').then(() => {
                        // Perangkat yang sudah pernah mengizinkan: perbarui langganan secara diam-diam
                        if (window.Notification && Notification.permission === 'granted') window.siatuSubscribePush().catch(() => {});
                    });
                });
            }

            // Sembunyikan bottom nav saat keyboard terbuka
            if (window.visualViewport) {
                window.visualViewport.addEventListener('resize', () => {
                    const nav = document.getElementById('bottom-nav');
                    if (nav) nav.style.display = (window.innerHeight - window.visualViewport.height > 150) ? 'none' : '';
                });
            }
        </script>

        @if (session()->has('success'))
            <script type="module">
                document.addEventListener('DOMContentLoaded', () => window.showAppAlert?.success(@js(session('success'))));
            </script>
        @endif
        @if (session()->has('error'))
            <script type="module">
                document.addEventListener('DOMContentLoaded', () => window.showAppAlert?.error(@js(session('error'))));
            </script>
        @endif
    </body>
</html>
