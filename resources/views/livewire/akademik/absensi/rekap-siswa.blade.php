<div>
    <x-slot:header>Rekap Kehadiran Siswa</x-slot:header>

    <div class="bg-surface rounded-xl shadow-sm border border-outline-variant p-6 mb-6">
        <h3 class="text-lg font-bold text-on-surface mb-4">Filter Data</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-on-surface mb-1">Tahun Ajaran</label>
                <select wire:model.live="tahun_ajaran_id" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    <option value="">Pilih Tahun Ajaran</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }} ({{ $ta->semester }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-on-surface mb-1">Kelas</label>
                <select wire:model.live="kelas_id" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    <option value="">Pilih Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-on-surface mb-1">Mata Pelajaran</label>
                <select wire:model.live="mapel_id" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    <option value="semua">Semua Mapel</option>
                    @foreach($mapels as $mapel)
                        <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-on-surface mb-1">Rentang Waktu</label>
                <select wire:model.live="tipe_waktu" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
                    <option value="bulan">Per Bulan</option>
                    <option value="hari">Per Hari</option>
                    <option value="semester">Satu Semester Penuh</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            @if($tipe_waktu == 'hari')
            <div>
                <label class="block text-sm font-medium text-on-surface mb-1">Pilih Tanggal</label>
                <input type="date" wire:model.live="tanggal_harian" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
            </div>
            @endif

            @if($tipe_waktu == 'bulan')
            <div>
                <label class="block text-sm font-medium text-on-surface mb-1">Pilih Bulan</label>
                <input type="month" wire:model.live="bulan_bulanan" class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition-shadow">
            </div>
            @endif
        </div>

        <div class="flex justify-between items-center mt-6 pt-4 border-t border-outline-variant">
            <div wire:loading wire:target="generateRekap" class="text-sm font-medium text-primary">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-primary inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menghitung rekap...
            </div>
            <div wire:loading.remove>
                @if(count($rekapData) > 0)
                    <p class="text-sm text-on-surface-variant">Menampilkan data {{ count($rekapData) }} siswa.</p>
                @endif
            </div>

            <button wire:click="exportExcel" @if(!$kelas_id || !$tahun_ajaran_id) disabled @endif class="px-6 py-2.5 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors flex items-center gap-2 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </button>
        </div>
    </div>

    @if(count($rekapData) > 0)
        <div class="bg-surface rounded-xl shadow-sm border border-outline-variant overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-surface-container-low text-on-surface-variant font-medium border-b border-outline-variant">
                        <tr>
                            <th rowspan="2" class="px-4 py-3 border-r border-outline-variant w-12 text-center align-middle">No</th>
                            <th rowspan="2" class="px-4 py-3 border-r border-outline-variant w-48 align-middle">Nama Siswa</th>
                            
                            @if($mapel_id != 'semua' && count($kolomTanggal) > 0)
                                <th colspan="{{ count($kolomTanggal) }}" class="px-4 py-2 border-r border-outline-variant text-center border-b">Detail Pertemuan (Tanggal)</th>
                            @endif

                            <th colspan="4" class="px-4 py-2 text-center border-b border-outline-variant">Total Rekap</th>
                        </tr>
                        <tr>
                            @if($mapel_id != 'semua' && count($kolomTanggal) > 0)
                                @foreach($kolomTanggal as $tgl)
                                    <th class="px-2 py-2 text-center border-r border-outline-variant text-xs">{{ \Carbon\Carbon::parse($tgl)->format('d/m') }}</th>
                                @endforeach
                            @endif
                            <th class="px-4 py-2 text-center border-r border-outline-variant text-green-600">Hadir</th>
                            <th class="px-4 py-2 text-center border-r border-outline-variant text-yellow-600">Sakit</th>
                            <th class="px-4 py-2 text-center border-r border-outline-variant text-blue-600">Izin</th>
                            <th class="px-4 py-2 text-center text-red-600">Alfa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @php $no = 1; @endphp
                        @foreach($rekapData as $id => $data)
                        <tr class="hover:bg-surface-container-lowest transition-colors">
                            <td class="px-4 py-3 border-r border-outline-variant text-center">{{ $no++ }}</td>
                            <td class="px-4 py-3 border-r border-outline-variant font-medium text-on-surface">{{ $data['siswa']->nama }}</td>
                            
                            @if($mapel_id != 'semua' && count($kolomTanggal) > 0)
                                @foreach($kolomTanggal as $tgl)
                                    @php
                                        $info = $data['per_tanggal'][$tgl] ?? null;
                                        $status = is_array($info) ? $info['status'] : ($info ?? '-');
                                        $bukti = is_array($info) ? $info['bukti_file'] : null;
                                        $colorClass = '';
                                        if($status == 'hadir') $colorClass = 'text-green-600 font-bold';
                                        if($status == 'sakit') $colorClass = 'text-yellow-600 font-bold';
                                        if($status == 'izin') $colorClass = 'text-blue-600 font-bold';
                                        if($status == 'alfa') $colorClass = 'text-red-600 font-bold';
                                    @endphp
                                    <td class="px-2 py-3 border-r border-outline-variant text-center {{ $colorClass }}">
                                        @if($status != '-')
                                            {{ strtoupper(substr($status, 0, 1)) }}
                                            @if($bukti)
                                                <a href="{{ Storage::disk('r2')->url($bukti) }}" target="_blank" class="block text-[10px] text-blue-500 hover:underline mt-1 font-normal" title="Lihat Bukti">
                                                    <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                    Bukti
                                                </a>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                            @endif

                            <td class="px-4 py-3 text-center border-r border-outline-variant font-medium text-green-700 bg-green-50/30">{{ $data['stats']['hadir'] }}</td>
                            <td class="px-4 py-3 text-center border-r border-outline-variant font-medium text-yellow-700 bg-yellow-50/30">{{ $data['stats']['sakit'] }}</td>
                            <td class="px-4 py-3 text-center border-r border-outline-variant font-medium text-blue-700 bg-blue-50/30">{{ $data['stats']['izin'] }}</td>
                            <td class="px-4 py-3 text-center font-medium text-red-700 bg-red-50/30">{{ $data['stats']['alfa'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        @if($kelas_id && $tahun_ajaran_id)
        <div class="text-center py-12 text-on-surface-variant bg-surface-container-lowest rounded-lg border border-dashed border-outline-variant">
            <svg class="mx-auto h-12 w-12 text-outline mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p class="text-base font-medium">Tidak ada data absensi untuk filter ini.</p>
        </div>
        @else
        <div class="text-center py-12 text-on-surface-variant bg-surface-container-lowest rounded-lg border border-dashed border-outline-variant">
            <svg class="mx-auto h-12 w-12 text-outline mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <p class="text-base font-medium">Silakan pilih Tahun Ajaran dan Kelas untuk melihat rekap.</p>
        </div>
        @endif
    @endif
</div>
