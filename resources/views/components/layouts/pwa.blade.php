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
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center p-1.5 shadow-lg shadow-indigo-200">
                        <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="Logo" class="w-full h-full object-contain filter brightness-0 invert">
                    </div>
                    <div>
                        <h1 class="font-bold text-slate-900 leading-tight tracking-tight text-lg">SITU-SMA</h1>
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
                
                <!-- Absen -->
                <a href="{{ route('akademik.absensi.guru.form') }}" 
                   class="relative flex flex-col items-center justify-center w-20 h-full group">
                    <div class="absolute inset-0 bg-indigo-50 rounded-2xl scale-0 {{ request()->routeIs('akademik.absensi.guru.form') ? 'scale-100' : '' }} transition-transform duration-300 origin-center -z-10 m-2"></div>
                    <svg class="w-6 h-6 mb-1 {{ request()->routeIs('akademik.absensi.guru.form') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-400 transition-colors' }}" fill="{{ request()->routeIs('akademik.absensi.guru.form') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="text-[11px] font-semibold {{ request()->routeIs('akademik.absensi.guru.form') ? 'text-indigo-700' : 'text-slate-500' }}">Absen</span>
                </a>

                <!-- Riwayat -->
                <a href="{{ route('akademik.absensi.guru.riwayat') }}" 
                   class="relative flex flex-col items-center justify-center w-20 h-full group">
                    <div class="absolute inset-0 bg-indigo-50 rounded-2xl scale-0 {{ request()->routeIs('akademik.absensi.guru.riwayat') ? 'scale-100' : '' }} transition-transform duration-300 origin-center -z-10 m-2"></div>
                    <svg class="w-6 h-6 mb-1 {{ request()->routeIs('akademik.absensi.guru.riwayat') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-400 transition-colors' }}" fill="{{ request()->routeIs('akademik.absensi.guru.riwayat') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-[11px] font-semibold {{ request()->routeIs('akademik.absensi.guru.riwayat') ? 'text-indigo-700' : 'text-slate-500' }}">Riwayat</span>
                </a>
                
                <!-- Jurnal (Placeholder for next phase) -->
                <a href="#" 
                   class="relative flex flex-col items-center justify-center w-20 h-full group">
                    <svg class="w-6 h-6 mb-1 text-slate-400 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span class="text-[11px] font-semibold text-slate-500">Jurnal</span>
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
