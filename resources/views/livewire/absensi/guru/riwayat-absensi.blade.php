<div>
    <x-slot name="header">
        Riwayat Absensi
    </x-slot>

    <div class="bg-surface-container-lowest rounded-xl p-6 tonal-shadow">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h2 class="text-xl font-bold tracking-tight text-on-surface">Riwayat Kehadiran Anda</h2>
            <div class="flex gap-2 w-full sm:w-auto">
                <select wire:model.live="filterBulan" class="rounded-md border-outline bg-surface px-3 py-2 text-on-surface shadow-sm focus:border-primary focus:ring focus:ring-primary/20 text-sm w-full sm:w-auto">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterTahun" class="rounded-md border-outline bg-surface px-3 py-2 text-on-surface shadow-sm focus:border-primary focus:ring focus:ring-primary/20 text-sm w-full sm:w-auto">
                    @foreach(range(date('Y')-2, date('Y')) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-outline-variant">
                        <th class="py-3 px-4 font-semibold text-sm text-on-surface-variant">Tanggal</th>
                        <th class="py-3 px-4 font-semibold text-sm text-on-surface-variant">Jenis</th>
                        <th class="py-3 px-4 font-semibold text-sm text-on-surface-variant">Status</th>
                        <th class="py-3 px-4 font-semibold text-sm text-on-surface-variant">Jam Absen</th>
                        <th class="py-3 px-4 font-semibold text-sm text-on-surface-variant">Keterangan</th>
                        <th class="py-3 px-4 font-semibold text-sm text-on-surface-variant">Foto Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $absen)
                        <tr class="border-b border-outline-variant/50 hover:bg-surface-variant/30 transition">
                            <td class="py-3 px-4 text-sm text-on-surface">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}</td>
                            <td class="py-3 px-4 text-sm text-on-surface">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $absen->jenis === 'masuk' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($absen->jenis) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-on-surface">
                                @php
                                    $color = match($absen->status) {
                                        'hadir' => 'bg-green-100 text-green-800',
                                        'terlambat' => 'bg-yellow-100 text-yellow-800',
                                        'izin', 'sakit' => 'bg-purple-100 text-purple-800',
                                        'alfa' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ ucfirst($absen->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-sm text-on-surface">{{ \Carbon\Carbon::parse($absen->waktu_absen)->format('H:i') }}</td>
                            <td class="py-3 px-4 text-sm text-on-surface">{{ $absen->keterangan ?? '-' }}</td>
                            <td class="py-3 px-4 text-sm text-on-surface">
                                @if($absen->foto_path)
                                    <a href="{{ Storage::url($absen->foto_path) }}" target="_blank" class="text-primary hover:underline">Lihat Foto</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-on-surface-variant">
                                Belum ada data absensi di bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $riwayat->links() }}
        </div>
    </div>
</div>
