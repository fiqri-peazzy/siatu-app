<div>
    <x-slot name="header">
        Monitoring Absensi Guru Hari Ini
    </x-slot>

    <div class="space-y-6">
        
        <!-- Statistik Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-surface-container-lowest rounded-xl p-4 tonal-shadow border-t-4 border-primary">
                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-widest">Total Guru</p>
                <p class="text-3xl font-bold text-on-surface mt-1">{{ $totalGuru }}</p>
            </div>
            <div class="bg-surface-container-lowest rounded-xl p-4 tonal-shadow border-t-4 border-green-500">
                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-widest">Hadir Tepat Waktu</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $statHadir }}</p>
            </div>
            <div class="bg-surface-container-lowest rounded-xl p-4 tonal-shadow border-t-4 border-yellow-500">
                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-widest">Terlambat</p>
                <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $statTelat }}</p>
            </div>
            <div class="bg-surface-container-lowest rounded-xl p-4 tonal-shadow border-t-4 border-purple-500">
                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-widest">Izin / Sakit</p>
                <p class="text-3xl font-bold text-purple-600 mt-1">{{ $statIzin }}</p>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl p-6 tonal-shadow flex flex-col">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <h3 class="text-lg font-bold text-on-surface">Daftar Kehadiran ({{ \Carbon\Carbon::today()->translatedFormat('d M Y') }})</h3>
                
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama guru..." class="rounded-md border-outline bg-surface px-3 py-2 text-on-surface shadow-sm focus:border-primary focus:ring focus:ring-primary/20 text-sm w-full sm:w-64">
                    
                    <select wire:model.live="filterStatus" class="rounded-md border-outline bg-surface px-3 py-2 text-on-surface shadow-sm focus:border-primary focus:ring focus:ring-primary/20 text-sm w-full sm:w-auto">
                        <option value="">Semua Status</option>
                        <option value="hadir">Hadir Tepat Waktu</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="alfa">Alfa</option>
                        <option value="belum_absen">Belum Absen</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-outline-variant">
                            <th class="py-3 px-4 font-semibold text-sm text-on-surface-variant">Nama Guru</th>
                            <th class="py-3 px-4 font-semibold text-sm text-on-surface-variant">Status</th>
                            <th class="py-3 px-4 font-semibold text-sm text-on-surface-variant">Jam Masuk</th>
                            <th class="py-3 px-4 font-semibold text-sm text-on-surface-variant">Jarak Lokasi</th>
                            <th class="py-3 px-4 font-semibold text-sm text-on-surface-variant">Aksi</th>
                        </tr>
                    </thead>
                    <tbody wire:poll.30s>
                        @forelse($gurus as $guru)
                            @php
                                $absenHariIni = $guru->absensis->first();
                                $status = $absenHariIni ? $absenHariIni->status : 'belum_absen';
                                
                                $color = match($status) {
                                    'hadir' => 'bg-green-100 text-green-800',
                                    'terlambat' => 'bg-yellow-100 text-yellow-800',
                                    'izin', 'sakit' => 'bg-purple-100 text-purple-800',
                                    'alfa' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <tr class="border-b border-outline-variant/50 hover:bg-surface-variant/30 transition">
                                <td class="py-3 px-4 text-sm text-on-surface">
                                    <p class="font-semibold">{{ $guru->nama }}</p>
                                    <p class="text-xs text-on-surface-variant">{{ $guru->jabatan }}</p>
                                </td>
                                <td class="py-3 px-4 text-sm text-on-surface">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                        {{ str_replace('_', ' ', ucfirst($status)) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm text-on-surface">
                                    {{ $absenHariIni ? \Carbon\Carbon::parse($absenHariIni->waktu_absen)->format('H:i') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-sm text-on-surface">
                                    {{ $absenHariIni && $absenHariIni->jarak_dari_sekolah_meter ? $absenHariIni->jarak_dari_sekolah_meter . ' meter' : '-' }}
                                </td>
                                <td class="py-3 px-4 text-sm text-on-surface">
                                    @if($absenHariIni && $absenHariIni->foto_path)
                                        <a href="{{ Storage::url($absenHariIni->foto_path) }}" target="_blank" class="text-primary hover:underline flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Foto
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-sm text-on-surface-variant">
                                    Tidak ada data guru yang cocok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $gurus->links() }}
            </div>
        </div>

    </div>
</div>
