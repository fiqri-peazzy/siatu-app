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
        <meta name="theme-color" content="#4f46e5">
        <link rel="apple-touch-icon" href="{{ asset('images/logo-removebg-preview.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <!-- Use modern geometric font (Outfit) as per web design guidelines -->
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { 
                font-family: 'Outfit', sans-serif;
                overscroll-behavior-y: none; 
                background-color: #f8fafc;
                -webkit-tap-highlight-color: transparent;
            }
            /* Glassmorphism utility */
            .glass-nav {
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-top: 1px solid rgba(255, 255, 255, 0.3);
            }
        </style>
    </head>
    <body class="antialiased text-slate-800 pb-24 pt-16">
        
        <!-- Premium Mobile Header -->
        <header class="fixed top-0 w-full z-50 glass-nav shadow-sm">
            <div class="px-5 h-16 flex items-center justify-between max-w-md mx-auto">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center p-1.5 shadow-sm shadow-slate-200/50 border border-slate-100">
                        <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="font-bold text-slate-900 leading-tight tracking-tight text-lg">SMAMGO</h1>
                        <p class="text-[10px] font-semibold text-indigo-600 uppercase tracking-widest leading-none">Guru Portal</p>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <button type="submit" class="w-10 h-10 rounded-full flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-all active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content (Max width for mobile simulation on desktop) -->
        <main class="w-full max-w-md mx-auto min-h-screen p-5">
            {{ $slot }}
        </main>

        <!-- Premium Bottom Navigation -->
        <nav class="fixed bottom-0 w-full z-50 glass-nav pb-safe shadow-[0_-10px_40px_rgba(0,0,0,0.04)]">
            <div class="flex justify-around items-center h-20 max-w-md mx-auto px-2">
                
                <!-- Beranda (Dashboard) -->
                <a href="{{ route('guru.dashboard') }}" 
                   class="relative flex flex-col items-center justify-center w-20 h-full group">
                    <div class="absolute inset-0 bg-indigo-50 rounded-2xl scale-0 {{ request()->routeIs('guru.dashboard') ? 'scale-100' : '' }} transition-transform duration-300 origin-center -z-10 m-2"></div>
                    <svg class="w-6 h-6 mb-1 {{ request()->routeIs('guru.dashboard') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-400 transition-colors' }}" fill="{{ request()->routeIs('guru.dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="text-[11px] font-semibold {{ request()->routeIs('guru.dashboard') ? 'text-indigo-700' : 'text-slate-500' }}">Beranda</span>
                </a>

                <!-- Jurnal -->
                <a href="{{ route('guru.dashboard') }}#jurnal" 
                   class="relative flex flex-col items-center justify-center w-20 h-full group">
                    <div class="absolute inset-0 bg-indigo-50 rounded-2xl scale-0 transition-transform duration-300 origin-center -z-10 m-2"></div>
                    <div class="relative">
                        <!-- Badge for uncompleted journals will be injected via livewire or Alpine if needed, for now just static structure -->
                        @livewire('mobile.guru.badge-jurnal')
                    </div>
                    <span class="text-[11px] font-semibold text-slate-500 mt-1">Jurnal</span>
                </a>

                <!-- Riwayat -->
                <a href="{{ route('guru.jurnal.riwayat') }}" 
                   class="relative flex flex-col items-center justify-center w-20 h-full group">
                    <div class="absolute inset-0 bg-indigo-50 rounded-2xl scale-0 {{ request()->routeIs('guru.jurnal.riwayat') ? 'scale-100' : '' }} transition-transform duration-300 origin-center -z-10 m-2"></div>
                    <svg class="w-6 h-6 mb-1 {{ request()->routeIs('guru.jurnal.riwayat') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-400 transition-colors' }}" fill="{{ request()->routeIs('guru.jurnal.riwayat') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-[11px] font-semibold {{ request()->routeIs('guru.jurnal.riwayat') ? 'text-indigo-700' : 'text-slate-500' }}">Riwayat</span>
                </a>
                
                <!-- Profil -->
                <a href="{{ route('guru.profil') }}" 
                   class="relative flex flex-col items-center justify-center w-20 h-full group">
                    <div class="absolute inset-0 bg-indigo-50 rounded-2xl scale-0 {{ request()->routeIs('guru.profil') ? 'scale-100' : '' }} transition-transform duration-300 origin-center -z-10 m-2"></div>
                    <svg class="w-6 h-6 mb-1 {{ request()->routeIs('guru.profil') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-400 transition-colors' }}" fill="{{ request()->routeIs('guru.profil') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-[11px] font-semibold {{ request()->routeIs('guru.profil') ? 'text-indigo-700' : 'text-slate-500' }}">Profil</span>
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
