<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SITU-SMA') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-background text-on-surface">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <aside class="w-72 bg-surface-container-low flex flex-col">
                <div class="p-8">
                    <h1 class="text-xl font-bold tracking-tight text-primary">SITU-SMA</h1>
                    <p class="text-xs text-on-surface-variant uppercase tracking-widest mt-1">Scholastic Authority</p>
                </div>

                <nav class="flex-1 px-4 space-y-1">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                    
                    <div class="pt-4 pb-2 px-4">
                        <span class="text-xs font-semibold text-on-surface-variant uppercase tracking-widest">Administrasi</span>
                    </div>
                    <x-nav-link href="#">
                        Surat Masuk
                    </x-nav-link>
                    <x-nav-link href="#">
                        Surat Keluar
                    </x-nav-link>

                    <div class="pt-4 pb-2 px-4">
                        <span class="text-xs font-semibold text-on-surface-variant uppercase tracking-widest">Akademik</span>
                    </div>
                    <x-nav-link href="#">
                        Absensi Guru
                    </x-nav-link>
                    <x-nav-link href="#">
                        Absensi Siswa
                    </x-nav-link>
                    <x-nav-link href="#">
                        Jurnal Mengajar
                    </x-nav-link>

                    <div class="pt-4 pb-2 px-4">
                        <span class="text-xs font-semibold text-on-surface-variant uppercase tracking-widest">Manajemen</span>
                    </div>
                    <x-nav-link href="#">
                        Sapras & Aset
                    </x-nav-link>
                    <x-nav-link href="#">
                        Dana BOS
                    </x-nav-link>
                    <x-nav-link href="#">
                        Kegiatan
                    </x-nav-link>

                    <div class="pt-4 pb-2 px-4">
                        <span class="text-xs font-semibold text-on-surface-variant uppercase tracking-widest">Data Master</span>
                    </div>
                    <x-nav-link href="{{ route('master.tahun-ajaran') }}" :active="request()->routeIs('master.tahun-ajaran')">
                        Tahun Ajaran
                    </x-nav-link>
                    <x-nav-link href="{{ route('master.guru') }}" :active="request()->routeIs('master.guru')">
                        Data Guru
                    </x-nav-link>
                    <x-nav-link href="{{ route('master.kelas') }}" :active="request()->routeIs('master.kelas')">
                        Data Kelas
                    </x-nav-link>
                    <x-nav-link href="{{ route('master.siswa') }}" :active="request()->routeIs('master.siswa')">
                        Data Siswa
                    </x-nav-link>
                </nav>

                <div class="p-4 bg-surface-container border-t-0">
                    <div class="flex items-center gap-3 px-4 py-2">
                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-on-primary font-bold">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                            <p class="text-xs text-on-surface-variant">{{ auth()->user()->role_label ?? '' }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="mt-2 px-4">
                        @csrf
                        <button type="submit" class="w-full text-left text-xs text-on-surface-variant hover:text-on-surface transition-colors py-1">
                            ← Keluar
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 flex flex-col">
                <header class="h-16 flex items-center justify-between px-8 bg-surface/80 backdrop-blur-md sticky top-0 z-10">
                    <div class="flex items-center gap-4">
                        <h2 class="text-lg font-medium text-on-surface-variant">{{ $header ?? '' }}</h2>
                    </div>
                    <div class="flex items-center gap-4">
                        <button class="p-2 text-on-surface-variant hover:bg-surface-container rounded-full transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path></svg>
                        </button>
                    </div>
                </header>

                <div class="p-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
