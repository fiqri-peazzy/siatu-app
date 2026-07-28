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
        <header class="fixed top-0 w-full z-50 bg-white/90 backdrop-blur-lg border-b border-line-200">
            <div class="px-5 h-16 flex items-center justify-between max-w-md mx-auto">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center p-1.5 shadow-sm border border-line-200 overflow-hidden">
                        <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div class="flex flex-col justify-center">
                        <h1 class="font-extrabold text-ink-900 leading-tight tracking-tight text-lg uppercase">SMAMGO</h1>
                        <p class="text-[10px] font-bold text-[#8b5cf6] uppercase tracking-[0.1em] leading-none mt-0.5">Guru Portal</p>
                    </div>
                </div>

                <div class="flex items-center">
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <button type="submit" class="w-10 h-10 rounded-full flex items-center justify-center text-slate-500 hover:text-danger-coral hover:bg-danger-coral/10 transition-colors active:scale-95">
                            <!-- Using standard standard logout icon instead of complex ones -->
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="w-full max-w-md mx-auto min-h-screen p-5">
            {{ $slot }}
        </main>

        <!-- Bottom Navigation: 5 tabs (Beranda, Jadwal, Jurnal, Riwayat, Profil) -->
        <nav class="fixed bottom-0 w-full z-50 bg-white border-t border-line-200" style="padding-bottom: env(safe-area-inset-bottom)">
            <div class="flex items-stretch h-16 max-w-md mx-auto">

                {{-- Beranda --}}
                <a href="{{ route('guru.dashboard') }}"
                   class="flex flex-col items-center justify-center flex-1 gap-0.5 transition-colors
                          {{ request()->routeIs('guru.dashboard') ? 'text-signal-blue' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-6 h-6" fill="{{ request()->routeIs('guru.dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[10px] font-semibold">Beranda</span>
                </a>

                {{-- Jadwal --}}
                <a href="{{ route('guru.dashboard') }}"
                   class="flex flex-col items-center justify-center flex-1 gap-0.5 transition-colors text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-[10px] font-semibold">Jadwal</span>
                </a>

                {{-- Jurnal (center with badge) --}}
                <a href="{{ route('guru.dashboard') }}#jurnal"
                   class="flex flex-col items-center justify-center flex-1 gap-0.5 transition-colors text-slate-400 hover:text-slate-600">
                    <div class="relative">
                        @livewire('mobile.guru.badge-jurnal')
                    </div>
                    <span class="text-[10px] font-semibold">Jurnal</span>
                </a>

                {{-- Riwayat --}}
                <a href="{{ route('guru.jurnal.riwayat') }}"
                   class="flex flex-col items-center justify-center flex-1 gap-0.5 transition-colors
                          {{ request()->routeIs('guru.jurnal.riwayat') ? 'text-signal-blue' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-6 h-6" fill="{{ request()->routeIs('guru.jurnal.riwayat') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-[10px] font-semibold">Riwayat</span>
                </a>

                {{-- Profil --}}
                <a href="{{ route('guru.profil') }}"
                   class="flex flex-col items-center justify-center flex-1 gap-0.5 transition-colors
                          {{ request()->routeIs('guru.profil') ? 'text-signal-blue' : 'text-slate-400 hover:text-slate-600' }}">
                    @if(request()->routeIs('guru.profil'))
                        {{-- Active: pill background --}}
                        <div class="bg-signal-blue rounded-full px-4 py-1 flex flex-col items-center gap-0.5 -mt-1">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-[10px] font-bold text-white">Profil</span>
                        </div>
                    @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-[10px] font-semibold">Profil</span>
                    @endif
                </a>

            </div>
        </nav>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Custom SweetAlert Styling for Mobile
            const Toast = Swal.mixin({
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded-2xl shadow-xl border border-slate-100 p-2',
                    title: 'font-outfit font-semibold text-sm',
                }
            });

            window.addEventListener('swal:success', event => {
                Toast.fire({
                    icon: 'success',
                    title: event.detail[0]
                });
            });

            window.addEventListener('swal:error', event => {
                Swal.fire({
                    title: 'Gagal!',
                    text: event.detail[0],
                    icon: 'error',
                    confirmButtonColor: '#4f46e5',
                    customClass: {
                        popup: 'rounded-3xl',
                        confirmButton: 'rounded-xl w-full py-3 text-base font-semibold',
                        title: 'font-outfit font-bold text-slate-800'
                    }
                });
            });
        </script>
        
        <!-- PWA Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js');
                });
            }
        </script>
    </body>
</html>
