<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SITU-SMA PWA') }}</title>

        <!-- PWA Meta -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo-removebg-preview.png') }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#0B1730">
        <link rel="apple-touch-icon" href="{{ asset('images/logo-removebg-preview.png') }}">

        <!-- Fonts: Inter (standard app font) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
                overscroll-behavior-y: none;
                background-color: #F5F7FC;
                -webkit-tap-highlight-color: transparent;
            }
        </style>
    </head>
    <body class="antialiased text-ink-900 pb-24 pt-16 bg-paper font-sans">

        <!-- Mobile Header (Standard App Bar) -->
        <header class="fixed top-0 w-full z-50 bg-[#121A2F]">
            <div class="px-5 h-16 flex items-center justify-between max-w-md mx-auto">
                <div class="flex items-center gap-3 text-white">
                    <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="Logo" class="w-9 h-9 object-contain drop-shadow-sm">
                    <div class="flex flex-col justify-center">
                        <h1 class="font-bold text-white leading-tight tracking-tight text-[15px] uppercase">SMAMGO</h1>
                        <span class="text-[9px] font-semibold text-sky-300 uppercase tracking-[0.1em]">
                            {{ auth()->user()->hasRole('guru') ? 'Guru Portal' : 'Staf Portal' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @php
                        $unreadCount = auth()->user() ? auth()->user()->unreadNotifications->count() : 0;
                    @endphp
                    <a href="{{ auth()->user()->hasRole('guru') ? route('guru.notifikasi') : '#' }}" class="relative w-10 h-10 rounded-full flex items-center justify-center text-slate-300 hover:text-white hover:bg-white/10 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if($unreadCount > 0)
                            <div class="absolute top-2 right-2 w-3 h-3 bg-red-500 rounded-full border-2 border-[#121A2F] flex items-center justify-center text-[8px] font-bold text-white">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</div>
                        @endif
                    </a>
                    <form method="POST" action="{{ route('logout') }}" x-data class="hidden">
                        @csrf
                        <button type="submit" id="btnLogout"></button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="w-full max-w-md mx-auto min-h-screen p-5">
            {{ $slot }}
        </main>

        <!-- Bottom Navigation: 5 tabs (Beranda, Jadwal, Jurnal, Riwayat, Profil) -->
        <nav id="bottom-nav" class="fixed bottom-0 w-full z-50 bg-[#121A2F]" style="padding-bottom: env(safe-area-inset-bottom)">
            <div class="flex items-stretch h-16 max-w-md mx-auto">

            @if(auth()->user()->hasRole('guru'))
                {{-- Beranda (Guru) --}}
                <a href="{{ route('guru.dashboard') }}"
                   class="flex flex-col items-center justify-center flex-1 gap-1 transition-colors relative">
                    @if(request()->routeIs('guru.dashboard'))
                        <div class="bg-[#3B65F3] rounded-xl px-4 py-1.5 flex flex-col items-center gap-0.5 mt-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" stroke="none" viewBox="0 0 24 24">
                                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                            </svg>
                            <span class="text-[10px] font-bold text-white leading-none mt-1">Beranda</span>
                        </div>
                    @else
                        <svg class="w-6 h-6 text-[#5E6A82]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="text-[10px] font-medium text-[#5E6A82] leading-none">Beranda</span>
                    @endif
                </a>

                {{-- Jadwal (Guru) --}}
                <a href="{{ route('guru.jadwal') }}"
                   class="flex flex-col items-center justify-center flex-1 gap-1 transition-colors relative">
                    @if(request()->routeIs('guru.jadwal'))
                        <div class="bg-[#3B65F3] rounded-xl px-4 py-1.5 flex flex-col items-center gap-0.5 mt-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-[10px] font-bold text-white leading-none mt-1">Jadwal</span>
                        </div>
                    @else
                        <svg class="w-6 h-6 text-[#5E6A82]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-[10px] font-medium text-[#5E6A82] leading-none">Jadwal</span>
                    @endif
                </a>

                {{-- Jurnal (Guru) --}}
                <a href="{{ route('guru.jurnal.riwayat') }}"
                   class="flex flex-col items-center justify-center flex-1 gap-1 transition-colors relative">
                    @if(request()->routeIs('guru.jurnal.riwayat'))
                        <div class="bg-[#3B65F3] rounded-xl px-4 py-1.5 flex flex-col items-center gap-0.5 mt-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span class="text-[10px] font-bold text-white leading-none mt-1">Jurnal</span>
                        </div>
                    @else
                        <svg class="w-6 h-6 text-[#5E6A82]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span class="text-[10px] font-medium text-[#5E6A82] leading-none">Jurnal</span>
                    @endif
                </a>

                {{-- Riwayat (Guru) --}}
                <a href="{{ route('akademik.absensi.guru.riwayat') }}"
                   class="flex flex-col items-center justify-center flex-1 gap-1 transition-colors relative">
                    @if(request()->routeIs('akademik.absensi.guru.riwayat'))
                        <div class="bg-[#3B65F3] rounded-xl px-4 py-1.5 flex flex-col items-center gap-0.5 mt-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" stroke="none" viewBox="0 0 24 24">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-[10px] font-bold text-white leading-none mt-1">Riwayat</span>
                        </div>
                    @else
                        <svg class="w-6 h-6 text-[#5E6A82]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-[10px] font-medium text-[#5E6A82] leading-none">Riwayat</span>
                    @endif
                </a>

                {{-- Profil (Guru) --}}
                <a href="{{ route('guru.profil') }}"
                   class="flex flex-col items-center justify-center flex-1 gap-1 transition-colors">
                    @if(request()->routeIs('guru.profil'))
                        <div class="bg-[#3B65F3] rounded-xl px-4 py-1.5 flex flex-col items-center gap-0.5 mt-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-[10px] font-bold text-white leading-none mt-1">Profil</span>
                        </div>
                    @else
                        <svg class="w-6 h-6 text-[#5E6A82]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-[10px] font-medium text-[#5E6A82] leading-none">Profil</span>
                    @endif
                </a>
            @else
                {{-- Tendik / Selain Guru --}}
                {{-- Beranda --}}
                <a href="{{ route('dashboard') }}"
                   class="flex flex-col items-center justify-center flex-1 gap-1 transition-colors relative">
                    @if(request()->routeIs('dashboard'))
                        <div class="bg-[#3B65F3] rounded-xl px-4 py-1.5 flex flex-col items-center gap-0.5 mt-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" stroke="none" viewBox="0 0 24 24">
                                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                            </svg>
                            <span class="text-[10px] font-bold text-white leading-none mt-1">Beranda</span>
                        </div>
                    @else
                        <svg class="w-6 h-6 text-[#5E6A82]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="text-[10px] font-medium text-[#5E6A82] leading-none">Beranda</span>
                    @endif
                </a>

                {{-- Absen --}}
                <a href="{{ route('akademik.absensi.tendik.form') }}"
                   class="flex flex-col items-center justify-center flex-1 gap-1 transition-colors relative">
                    @if(request()->routeIs('akademik.absensi.tendik.form'))
                        <div class="bg-[#3B65F3] rounded-xl px-4 py-1.5 flex flex-col items-center gap-0.5 mt-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-[10px] font-bold text-white leading-none mt-1">Absen</span>
                        </div>
                    @else
                        <svg class="w-6 h-6 text-[#5E6A82]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-[10px] font-medium text-[#5E6A82] leading-none">Absen</span>
                    @endif
                </a>

                {{-- Riwayat --}}
                <a href="{{ route('akademik.absensi.tendik.riwayat') }}"
                   class="flex flex-col items-center justify-center flex-1 gap-1 transition-colors relative">
                    @if(request()->routeIs('akademik.absensi.tendik.riwayat'))
                        <div class="bg-[#3B65F3] rounded-xl px-4 py-1.5 flex flex-col items-center gap-0.5 mt-2">
                            <svg class="w-5 h-5 text-white" fill="currentColor" stroke="none" viewBox="0 0 24 24">
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-[10px] font-bold text-white leading-none mt-1">Riwayat</span>
                        </div>
                    @else
                        <svg class="w-6 h-6 text-[#5E6A82]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-[10px] font-medium text-[#5E6A82] leading-none">Riwayat</span>
                    @endif
                </a>
                
                {{-- Profil (Tendik / Non-Guru) - using profile page link placeholder or reuse --}}
                {{-- Currently Tendik doesn't have a dedicated PWA profile page, we'll link to dashboard or profile if any --}}
                <a href="#" class="flex flex-col items-center justify-center flex-1 gap-1 transition-colors relative opacity-50 cursor-not-allowed">
                    <svg class="w-6 h-6 text-[#5E6A82]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-[10px] font-medium text-[#5E6A82] leading-none">Profil</span>
                </a>
            @endif

            </div>
        </nav>


        
        <!-- PWA Service Worker Registration -->
        <script>
            function urlBase64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding)
                    .replace(/\-/g, '+')
                    .replace(/_/g, '/');
                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);
                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            }

            if ('serviceWorker' in navigator && 'PushManager' in window) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js').then(function(registration) {
                        console.log('ServiceWorker registered:', registration);
                        
                        // Setup Push Notification
                        Notification.requestPermission().then(function(permission) {
                            if (permission === 'granted') {
                                subscribeUserToPush(registration);
                            }
                        });
                    });
                });
            }

            function subscribeUserToPush(registration) {
                const vapidPublicKey = '{{ config('webpush.vapid.public_key') }}';
                if (!vapidPublicKey) return;

                const convertedVapidKey = urlBase64ToUint8Array(vapidPublicKey);

                registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: convertedVapidKey
                }).then(function(subscription) {
                    // Send subscription to server
                    fetch('/webpush/subscribe', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(subscription)
                    });
                }).catch(function(err) {
                    console.log('Failed to subscribe the user: ', err);
                });
            }
            
            // Auto-hide bottom nav when keyboard is active
            if (window.visualViewport) {
                const bottomNav = document.getElementById('bottom-nav');
                window.visualViewport.addEventListener('resize', () => {
                    if (window.innerHeight - window.visualViewport.height > 150) {
                        bottomNav.style.display = 'none';
                    } else {
                        bottomNav.style.display = '';
                    }
                });
            }
        </script>
        
        <!-- Handle Session Flashes with SweetAlert2 -->
        @if (session()->has('success'))
            <script type="module">
                document.addEventListener('DOMContentLoaded', function () {
                    if (window.showAppAlert) {
                        window.showAppAlert.success("{{ session('success') }}");
                    }
                });
            </script>
        @endif

        @if (session()->has('error'))
            <script type="module">
                document.addEventListener('DOMContentLoaded', function () {
                    if (window.showAppAlert) {
                        window.showAppAlert.error("{{ session('error') }}");
                    }
                });
            </script>
        @endif
        
        <script type="module">
            document.addEventListener('DOMContentLoaded', function () {
                window.addEventListener('swal:success', event => {
                    let msg = event.detail.message ?? (event.detail[0] && event.detail[0].message) ?? event.detail[0] ?? event.detail;
                    if (window.showAppAlert) window.showAppAlert.success(msg);
                });
                window.addEventListener('swal:error', event => {
                    let msg = event.detail.message ?? (event.detail[0] && event.detail[0].message) ?? event.detail[0] ?? event.detail;
                    if (window.showAppAlert) window.showAppAlert.error(msg);
                });
                window.addEventListener('swal:warning', event => {
                    let msg = event.detail.message ?? (event.detail[0] && event.detail[0].message) ?? event.detail[0] ?? event.detail;
                    if (window.showAppAlert) window.showAppAlert.warning(msg);
                });
                window.addEventListener('swal:info', event => {
                    let msg = event.detail.message ?? (event.detail[0] && event.detail[0].message) ?? event.detail[0] ?? event.detail;
                    if (window.showAppAlert) window.showAppAlert.info(msg);
                });
            });
        </script>

    </body>
</html>
