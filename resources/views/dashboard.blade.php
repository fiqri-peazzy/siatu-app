<x-layouts.app>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="space-y-8">

        {{-- Greeting --}}
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-on-surface" style="letter-spacing: -0.02em;">
                Selamat datang, {{ auth()->user()->name }}
            </h2>
                {{ now()->translatedFormat('l, d F Y') }} · Tahun Pelajaran 2024/2025
            </p>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="bg-surface-container-lowest rounded-xl p-6 tonal-shadow">
                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-widest">Surat Masuk</p>
                <p class="text-4xl font-bold text-on-surface mt-2" style="letter-spacing: -0.02em;">—</p>
                <p class="text-xs text-on-surface-variant mt-1">Bulan ini</p>
            </div>

            <div class="bg-surface-container-lowest rounded-xl p-6 tonal-shadow">
                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-widest">Surat Keluar</p>
                <p class="text-4xl font-bold text-on-surface mt-2" style="letter-spacing: -0.02em;">—</p>
                <p class="text-xs text-on-surface-variant mt-1">Bulan ini</p>
            </div>

            <div class="bg-surface-container-lowest rounded-xl p-6 tonal-shadow">
                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-widest">Guru & Staf</p>
                <p class="text-4xl font-bold text-on-surface mt-2" style="letter-spacing: -0.02em;">{{ \App\Models\Master\MasterGuru::count() }}</p>
                <p class="text-xs text-on-surface-variant mt-1">Aktif terdaftar</p>
            </div>

            <div class="rounded-xl p-6 signature-gradient tonal-shadow">
                <p class="text-xs font-semibold text-white/70 uppercase tracking-widest">Pengguna Sistem</p>
                <p class="text-4xl font-bold text-white mt-2" style="letter-spacing: -0.02em;">{{ \App\Models\User::count() }}</p>
                <p class="text-xs text-white/70 mt-1">Semua role</p>
            </div>

        </div>

        {{-- Role Info --}}
        <div class="bg-surface-container-lowest rounded-xl p-6 tonal-shadow">
            <h3 class="text-sm font-semibold text-on-surface-variant uppercase tracking-widest mb-4">Informasi Akun</h3>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full signature-gradient flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div>
                    <p class="font-semibold text-on-surface">{{ auth()->user()->name }}</p>
                    <p class="text-sm text-on-surface-variant">{{ auth()->user()->email }}</p>
                    <span class="inline-block mt-1 px-3 py-0.5 bg-secondary-container text-secondary text-xs font-semibold rounded-full">
                        {{ auth()->user()->role_label }}
                    </span>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
