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
                            <th class="py-3 px-4 font-semibold text-sm text-on-surface-variant">Waktu Masuk</th>
                            <th class="py-3 px-4 font-semibold text-sm text-on-surface-variant">Waktu Pulang</th>
                            <th class="py-3 px-4 font-semibold text-sm text-on-surface-variant">Info Jarak</th>
                            <th class="py-3 px-4 font-semibold text-sm text-on-surface-variant">Aksi / Foto</th>
                        </tr>
                    </thead>
                    <tbody wire:poll.30s>
                        @forelse($gurus as $guru)
                            @php
                                $absenMasuk = $guru->absensis->where('jenis', 'masuk')->first();
                                $absenPulang = $guru->absensis->where('jenis', 'pulang')->first();
                                $status = $absenMasuk ? $absenMasuk->status : 'belum_absen';
                                
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
                                <td class="py-3 px-4 text-sm text-on-surface font-medium">
                                    {{ $absenMasuk ? \Carbon\Carbon::parse($absenMasuk->waktu_absen)->format('H:i') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-sm text-on-surface font-medium">
                                    {{ $absenPulang ? \Carbon\Carbon::parse($absenPulang->waktu_absen)->format('H:i') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-xs text-on-surface-variant leading-relaxed">
                                    @if($absenMasuk)
                                        <div class="mb-1"><strong>M:</strong> {{ $absenMasuk->jarak_dari_sekolah_meter }}m</div>
                                    @endif
                                    @if($absenPulang)
                                        <div><strong>P:</strong> {{ $absenPulang->jarak_dari_sekolah_meter }}m</div>
                                    @endif
                                    @if(!$absenMasuk && !$absenPulang)
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm text-on-surface">
                                    <div class="flex items-center gap-3">
                                        @if($absenMasuk && $absenMasuk->foto_path)
                                            <a href="{{ Storage::url($absenMasuk->foto_path) }}" target="_blank" class="text-primary hover:underline flex items-center text-xs bg-primary/5 px-2 py-1 rounded">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                                Masuk
                                            </a>
                                        @endif
                                        @if($absenPulang && $absenPulang->foto_path)
                                            <a href="{{ Storage::url($absenPulang->foto_path) }}" target="_blank" class="text-emerald-600 hover:underline flex items-center text-xs bg-emerald-50 px-2 py-1 rounded">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                                Pulang
                                            </a>
                                        @endif
                                    </div>
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
