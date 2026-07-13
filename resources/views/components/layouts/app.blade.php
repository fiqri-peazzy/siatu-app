<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SITU-SMA') }}</title>

        <!-- PWA -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo-removebg-preview.png') }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#4f46e5">
        <link rel="apple-touch-icon" href="{{ asset('images/logo-removebg-preview.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-background text-on-surface">
        <div class="min-h-screen flex overflow-hidden bg-background" x-data="{ 
            sidebarOpen: false, 
            sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
            init() {
                this.$watch('sidebarCollapsed', val => localStorage.setItem('sidebarCollapsed', val))
            }
        }">
            
            <!-- Mobile overlay -->
            <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-20 lg:hidden" x-transition.opacity></div>

            <!-- Sidebar -->
            <aside class="bg-surface-container-low flex flex-col transition-all duration-300 z-30 fixed inset-y-0 left-0 lg:static lg:translate-x-0 w-72"
                   :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen, 'lg:w-20': sidebarCollapsed }">
                
                <div class="p-4 h-16 flex items-center justify-center lg:justify-start border-b border-surface-container-high gap-3 overflow-hidden">
                    <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="Logo" class="w-10 h-10 object-contain flex-shrink-0">
                    <div class="whitespace-nowrap transition-opacity duration-200" :class="{ 'lg:hidden': sidebarCollapsed }">
                        <h1 class="text-sm font-bold tracking-tight text-primary">SITU-SMAMGO</h1>
                        <p class="text-[10px] text-on-surface-variant uppercase tracking-widest mt-0.5">Panel Admin</p>
                    </div>
                </div>

                <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto overflow-x-hidden custom-scrollbar">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                        </x-slot:icon>
                        Dashboard
                    </x-nav-link>
                    
                    <div class="pt-4 pb-2 px-2" :class="{ 'lg:text-center': sidebarCollapsed }">
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest transition-opacity duration-200" :class="{ 'lg:hidden': sidebarCollapsed }">Administrasi</span>
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest transition-opacity duration-200 hidden" :class="{ 'lg:inline': sidebarCollapsed }">...</span>
                    </div>
                    <x-nav-link href="{{ route('administrasi.surat-masuk.index') }}" :active="request()->routeIs('administrasi.surat-masuk.*')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        </x-slot:icon>
                        Surat Masuk
                    </x-nav-link>
                    <x-nav-link href="{{ route('administrasi.surat-keluar.index') }}" :active="request()->routeIs('administrasi.surat-keluar.*')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                        </x-slot:icon>
                        Surat Keluar
                    </x-nav-link>
              
                    <div class="pt-4 pb-2 px-2" :class="{ 'lg:text-center': sidebarCollapsed }">
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest transition-opacity duration-200" :class="{ 'lg:hidden': sidebarCollapsed }">Akademik</span>
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest transition-opacity duration-200 hidden" :class="{ 'lg:inline': sidebarCollapsed }">...</span>
                    </div>
                    
                    @if(auth()->user()->hasRole('guru'))
                    <x-nav-link href="{{ route('akademik.absensi.guru.form') }}" :active="request()->routeIs('akademik.absensi.guru.form')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zm-7.518-.267A8.25 8.25 0 1120.25 10.5M8.288 14.212A5.25 5.25 0 1117.25 10.5" /></svg>
                        </x-slot:icon>
                        Absen Sekarang
                    </x-nav-link>
                    <x-nav-link href="{{ route('akademik.absensi.guru.riwayat') }}" :active="request()->routeIs('akademik.absensi.guru.riwayat')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </x-slot:icon>
                        Riwayat Absensi
                    </x-nav-link>
                    @endif

                    @if(auth()->user()->hasAnyRole(['admin-tu', 'kepala-sekolah', 'super-admin']))
                    <x-nav-link href="{{ route('akademik.absensi.monitoring') }}" :active="request()->routeIs('akademik.absensi.monitoring')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </x-slot:icon>
                        Monitoring Absensi
                    </x-nav-link>
                    @endif

                    <x-nav-link href="#">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                        </x-slot:icon>
                        Jurnal Mengajar
                    </x-nav-link>

                    <div class="pt-4 pb-2 px-2" :class="{ 'lg:text-center': sidebarCollapsed }">
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest transition-opacity duration-200" :class="{ 'lg:hidden': sidebarCollapsed }">Manajemen</span>
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest transition-opacity duration-200 hidden" :class="{ 'lg:inline': sidebarCollapsed }">...</span>
                    </div>
                    <x-nav-link href="#">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" /></svg>
                        </x-slot:icon>
                        Sapras & Aset
                    </x-nav-link>
                    <x-nav-link href="#">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </x-slot:icon>
                        Dana BOS
                    </x-nav-link>
                    <x-nav-link href="#">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                        </x-slot:icon>
                        Kegiatan
                    </x-nav-link>

                    <div class="pt-4 pb-2 px-2" :class="{ 'lg:text-center': sidebarCollapsed }">
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest transition-opacity duration-200" :class="{ 'lg:hidden': sidebarCollapsed }">Data Master</span>
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest transition-opacity duration-200 hidden" :class="{ 'lg:inline': sidebarCollapsed }">...</span>
                    </div>
                    <x-nav-link href="{{ route('master.tahun-ajaran') }}" :active="request()->routeIs('master.tahun-ajaran')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" /></svg>
                        </x-slot:icon>
                        Tahun Ajaran
                    </x-nav-link>
                    <x-nav-link href="{{ route('master.guru') }}" :active="request()->routeIs('master.guru')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                        </x-slot:icon>
                        Data Guru
                    </x-nav-link>
                    <x-nav-link href="{{ route('master.tendik') }}" :active="request()->routeIs('master.tendik')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                        </x-slot:icon>
                        Data Tendik
                    </x-nav-link>
                    <x-nav-link href="{{ route('master.mapel') }}" :active="request()->routeIs('master.mapel')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                        </x-slot:icon>
                        Data Mapel
                    </x-nav-link>
                    <x-nav-link href="{{ route('master.kelas') }}" :active="request()->routeIs('master.kelas')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" /></svg>
                        </x-slot:icon>
                        Data Kelas
                    </x-nav-link>
                    <x-nav-link href="{{ route('master.siswa') }}" :active="request()->routeIs('master.siswa')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                        </x-slot:icon>
                        Data Siswa
                    </x-nav-link>
                          
                    @if(auth()->user()->hasAnyRole(['admin-tu', 'super-admin']))
                    <x-nav-link href="{{ route('administrasi.pengaturan-lokasi') }}" :active="request()->routeIs('administrasi.pengaturan-lokasi')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" /></svg>
                        </x-slot:icon>
                        Pengaturan Lokasi
                    </x-nav-link>

                    <div class="pt-4 pb-2 px-2" :class="{ 'lg:text-center': sidebarCollapsed }">
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest transition-opacity duration-200" :class="{ 'lg:hidden': sidebarCollapsed }">Sistem</span>
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest transition-opacity duration-200 hidden" :class="{ 'lg:inline': sidebarCollapsed }">...</span>
                    </div>
                    <x-nav-link href="{{ route('sistem.user') }}" :active="request()->routeIs('sistem.user')">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                        </x-slot:icon>
                        Manajemen Pengguna
                    </x-nav-link>
                    @endif
                </nav>

                <div class="p-2 bg-surface-container border-t border-surface-container-high transition-all duration-300">
                    <div class="flex items-center gap-3 p-2 rounded-xl transition-all duration-300" :class="{ 'justify-center': sidebarCollapsed }">
                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-on-primary font-bold flex-shrink-0">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="overflow-hidden transition-all duration-200 w-full" :class="{ 'lg:hidden': sidebarCollapsed }">
                            <p class="text-sm font-semibold truncate">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                            <p class="text-[10px] text-on-surface-variant truncate">{{ auth()->user()->role_label ?? '' }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="mt-2 px-2 pb-2">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 text-sm font-medium text-on-surface-variant hover:text-error transition-colors p-2 rounded-lg hover:bg-error/10" :class="{ 'justify-center': sidebarCollapsed }">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                            <span class="whitespace-nowrap transition-opacity duration-200" :class="{ 'lg:hidden': sidebarCollapsed }">
                                Keluar
                            </span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
                <header class="h-16 flex items-center justify-between px-4 lg:px-8 bg-surface/80 backdrop-blur-md sticky top-0 z-10 border-b border-surface-container-low flex-shrink-0">
                    <div class="flex items-center gap-4 min-w-0">
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 text-on-surface-variant hover:bg-surface-container rounded-full transition-colors flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                        
                        <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:block p-2 -ml-2 text-on-surface-variant hover:bg-surface-container rounded-full transition-colors flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-300" :class="{ 'rotate-180': sidebarCollapsed }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                        </button>
                        
                        <h2 class="text-lg font-medium text-on-surface-variant truncate">{{ $header ?? '' }}</h2>
                    </div>
                    <div class="flex items-center gap-4 flex-shrink-0">
                        <!-- Header actions can go here -->
                    </div>
                </header>

                <div class="flex-1 overflow-y-auto overflow-x-hidden p-4 lg:p-8">
                    <div class="w-full max-w-full">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
        
        <!-- Handle Session Flashes with SweetAlert2 -->
        @if (session()->has('success'))
            <script type="module">
                document.addEventListener('DOMContentLoaded', function () {
                    window.Swal.fire({
                        title: 'Berhasil!',
                        text: "{{ session('success') }}",
                        icon: 'success',
                        confirmButtonColor: '#4f46e5',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'rounded-md shadow-sm'
                        }
                    });
                });
            </script>
        @endif

        @if (session()->has('error'))
            <script type="module">
                document.addEventListener('DOMContentLoaded', function () {
                    window.Swal.fire({
                        title: 'Error!',
                        text: "{{ session('error') }}",
                        icon: 'error',
                        confirmButtonColor: '#ef4444',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'rounded-md shadow-sm bg-red-500'
                        }
                    });
                });
            </script>
        @endif

        <!-- PWA Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js').then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    }, function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
                });
            }
        </script>
    </body>
</html>
