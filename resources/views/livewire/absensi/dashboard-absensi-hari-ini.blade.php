<div>
    <x-slot name="header">
        Monitoring Absensi Guru
    </x-slot>

    <div class="space-y-6">
        
        <!-- Statistik Cards (Modernized) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 to-white dark:from-blue-900/20 dark:to-surface rounded-2xl p-5 shadow-sm border border-blue-100 dark:border-blue-800/30 group hover:shadow-md transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-100 dark:bg-blue-900/30 rounded-full opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">Total Guru</p>
                        <p class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $totalGuru }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/40 rounded-xl text-blue-600 dark:text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden bg-gradient-to-br from-green-50 to-white dark:from-emerald-900/20 dark:to-surface rounded-2xl p-5 shadow-sm border border-green-100 dark:border-emerald-800/30 group hover:shadow-md transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-green-100 dark:bg-emerald-900/30 rounded-full opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-green-600 dark:text-emerald-400 uppercase tracking-wider mb-1">Total Hadir</p>
                        <p class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $statHadir }}</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-emerald-900/40 rounded-xl text-green-600 dark:text-emerald-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden bg-gradient-to-br from-amber-50 to-white dark:from-amber-900/20 dark:to-surface rounded-2xl p-5 shadow-sm border border-amber-100 dark:border-amber-800/30 group hover:shadow-md transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-100 dark:bg-amber-900/30 rounded-full opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-1">Terlambat</p>
                        <p class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $statTelat }}</p>
                    </div>
                    <div class="p-3 bg-amber-100 dark:bg-amber-900/40 rounded-xl text-amber-600 dark:text-amber-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden bg-gradient-to-br from-purple-50 to-white dark:from-purple-900/20 dark:to-surface rounded-2xl p-5 shadow-sm border border-purple-100 dark:border-purple-800/30 group hover:shadow-md transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-100 dark:bg-purple-900/30 rounded-full opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider mb-1">Izin / Sakit</p>
                        <p class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ $statIzin }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/40 rounded-xl text-purple-600 dark:text-purple-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-outline-variant/30 flex flex-col">
            
            <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-8 gap-6">
                <div>
                    <h3 class="text-xl font-bold text-on-surface flex items-center gap-2">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Data Kehadiran Guru
                    </h3>
                    <p class="text-sm text-on-surface-variant mt-1">
                        Menampilkan data dari <span class="font-semibold">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}</span> 
                        @if($isRange)
                            sampai <span class="font-semibold">{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</span>
                        @endif
                    </p>
                </div>
                
                <div class="flex flex-col md:flex-row gap-3 w-full xl:w-auto flex-wrap">
                    <div class="flex items-center gap-2 bg-surface-variant/30 rounded-xl p-1 border border-outline-variant/50">
                        <input type="date" wire:model.live="startDate" class="bg-transparent border-none focus:ring-0 text-sm text-on-surface px-3 py-2 cursor-pointer">
                        <span class="text-on-surface-variant text-sm font-medium">s/d</span>
                        <input type="date" wire:model.live="endDate" class="bg-transparent border-none focus:ring-0 text-sm text-on-surface px-3 py-2 cursor-pointer">
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-on-surface-variant" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama guru..." class="pl-10 rounded-xl border-outline-variant/50 bg-surface px-4 py-2 text-on-surface shadow-sm focus:border-primary focus:ring-primary/20 text-sm w-full md:w-64 transition-all">
                    </div>
                    
                    @if(!$isRange)
                    <select wire:model.live="filterStatus" class="rounded-xl border-outline-variant/50 bg-surface px-4 py-2 text-on-surface shadow-sm focus:border-primary focus:ring-primary/20 text-sm w-full md:w-auto cursor-pointer transition-all">
                        <option value="">Semua Status</option>
                        <option value="hadir">Hadir Tepat Waktu</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="alfa">Alfa</option>
                        <option value="belum_absen">Belum Absen</option>
                    </select>
                    @endif

                    <button wire:click="exportExcel" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl shadow-sm shadow-emerald-500/30 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 w-full md:w-auto group">
                        <svg class="w-4 h-4 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Export Excel
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-outline-variant/30">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-surface-variant/30 border-b border-outline-variant/30">
                            <th class="py-3.5 px-5 font-semibold text-xs text-on-surface-variant uppercase tracking-wider">Nama Guru</th>
                            @if(!$isRange)
                                <th class="py-3.5 px-5 font-semibold text-xs text-on-surface-variant uppercase tracking-wider">Status</th>
                                <th class="py-3.5 px-5 font-semibold text-xs text-on-surface-variant uppercase tracking-wider">Waktu Masuk</th>
                                <th class="py-3.5 px-5 font-semibold text-xs text-on-surface-variant uppercase tracking-wider">Waktu Pulang</th>
                                <th class="py-3.5 px-5 font-semibold text-xs text-on-surface-variant uppercase tracking-wider">Info Jarak</th>
                                <th class="py-3.5 px-5 font-semibold text-xs text-on-surface-variant uppercase tracking-wider">Aksi / Foto</th>
                            @else
                                <th class="py-3.5 px-5 font-semibold text-xs text-on-surface-variant uppercase tracking-wider text-center">Total Hadir</th>
                                <th class="py-3.5 px-5 font-semibold text-xs text-on-surface-variant uppercase tracking-wider text-center">Terlambat</th>
                                <th class="py-3.5 px-5 font-semibold text-xs text-on-surface-variant uppercase tracking-wider text-center">Izin/Sakit</th>
                                <th class="py-3.5 px-5 font-semibold text-xs text-on-surface-variant uppercase tracking-wider text-center">Alfa</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody wire:poll.60s class="divide-y divide-outline-variant/20">
                        @forelse($gurus as $guru)
                            @if(!$isRange)
                                @php
                                    $absenMasuk = $guru->absensis->where('jenis', 'masuk')->first();
                                    $absenPulang = $guru->absensis->where('jenis', 'pulang')->first();
                                    $status = $absenMasuk ? $absenMasuk->status : 'belum_absen';
                                    
                                    $color = match($status) {
                                        'hadir' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800',
                                        'terlambat' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800',
                                        'izin', 'sakit' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 border border-purple-200 dark:border-purple-800',
                                        'alfa' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800',
                                        default => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700'
                                    };
                                @endphp
                                <tr class="hover:bg-primary/5 transition-colors group">
                                    <td class="py-4 px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold shadow-inner">
                                                {{ substr($guru->nama, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-sm text-on-surface">{{ $guru->nama }}</p>
                                                <p class="text-xs text-on-surface-variant mt-0.5">{{ $guru->jabatan ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5">
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-lg {{ $color }}">
                                            {{ str_replace('_', ' ', ucfirst($status)) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-sm text-on-surface font-medium">
                                        @if($absenMasuk)
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ \Carbon\Carbon::parse($absenMasuk->waktu_absen)->format('H:i') }}
                                            </div>
                                        @else
                                            <span class="text-on-surface-variant">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-5 text-sm text-on-surface font-medium">
                                        @if($absenPulang)
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ \Carbon\Carbon::parse($absenPulang->waktu_absen)->format('H:i') }}
                                            </div>
                                        @else
                                            <span class="text-on-surface-variant">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-5 text-xs text-on-surface-variant">
                                        <div class="space-y-1.5">
                                            @if($absenMasuk)
                                                <div class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px]">M</span> {{ $absenMasuk->jarak_dari_sekolah_meter }}m</div>
                                            @endif
                                            @if($absenPulang)
                                                <div class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[10px]">P</span> {{ $absenPulang->jarak_dari_sekolah_meter }}m</div>
                                            @endif
                                            @if(!$absenMasuk && !$absenPulang)
                                                -
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-5">
                                        <div class="flex items-center gap-2">
                                            @if($absenMasuk && $absenMasuk->foto_path)
                                                <a href="{{ Storage::url($absenMasuk->foto_path) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1.5 rounded-lg transition-colors border border-emerald-200">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                                    Masuk
                                                </a>
                                            @endif
                                            @if($absenPulang && $absenPulang->foto_path)
                                                <a href="{{ Storage::url($absenPulang->foto_path) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 px-2.5 py-1.5 rounded-lg transition-colors border border-blue-200">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                                    Pulang
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @php
                                    $absensisMasuk = $guru->absensis->where('jenis', 'masuk');
                                    $hadir = $absensisMasuk->where('status', 'hadir')->count();
                                    $terlambat = $absensisMasuk->where('status', 'terlambat')->count();
                                    $izinSakit = $absensisMasuk->whereIn('status', ['izin', 'sakit'])->count();
                                    $alfa = $absensisMasuk->where('status', 'alfa')->count();
                                @endphp
                                <tr class="hover:bg-primary/5 transition-colors group">
                                    <td class="py-4 px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold shadow-inner">
                                                {{ substr($guru->nama, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-sm text-on-surface">{{ $guru->nama }}</p>
                                                <p class="text-xs text-on-surface-variant mt-0.5">{{ $guru->jabatan ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[2.5rem] h-8 rounded-lg bg-emerald-50 text-emerald-700 font-bold text-sm border border-emerald-100">
                                            {{ $hadir }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[2.5rem] h-8 rounded-lg bg-amber-50 text-amber-700 font-bold text-sm border border-amber-100">
                                            {{ $terlambat }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[2.5rem] h-8 rounded-lg bg-purple-50 text-purple-700 font-bold text-sm border border-purple-100">
                                            {{ $izinSakit }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[2.5rem] h-8 rounded-lg bg-red-50 text-red-700 font-bold text-sm border border-red-100">
                                            {{ $alfa }}
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="{{ $isRange ? 5 : 6 }}" class="py-12">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="w-16 h-16 bg-surface-variant rounded-full flex items-center justify-center mb-4 text-on-surface-variant opacity-50">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <p class="text-base font-semibold text-on-surface">Tidak ada data ditemukan</p>
                                        <p class="text-sm text-on-surface-variant mt-1">Coba sesuaikan filter pencarian atau tanggal.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 border-t border-outline-variant/30 pt-4">
                {{ $gurus->links(data: ['scrollTo' => false]) }}
            </div>
        </div>

    </div>
</div>
