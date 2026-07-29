<div>
    <x-slot name="header">
        Rekapan Jurnal Mengajar
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        
        <!-- Header & Toolbar -->
        <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path></svg>
                <h3 class="text-xl font-bold text-gray-800">Jurnal Mengajar</h3>
            </div>
            
            <div class="flex items-center gap-2">
                <label class="text-sm font-bold text-gray-700">Tanggal</label>
                <input type="date" wire:model.live="tanggalMulai" class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:ring-1 focus:ring-purple-600 focus:border-purple-600">
                <span class="text-sm font-bold text-gray-700">s/d</span>
                <input type="date" wire:model.live="tanggalSelesai" class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:ring-1 focus:ring-purple-600 focus:border-purple-600">
            </div>
        </div>

        <!-- Filters (2nd Row) -->
        <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/50">
            <div class="flex items-center gap-2">
                <a href="{{ route('supervisi.jurnal.export', ['start_date' => $tanggalMulai, 'end_date' => $tanggalSelesai, 'guru' => $filterGuru, 'mapel' => $filterMapel, 'kelas' => $filterKelas]) }}" target="_blank" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    XLS
                </a>
            </div>

            <div class="flex items-center gap-4 w-full sm:w-auto">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-bold text-gray-700 whitespace-nowrap">Tampilkan</label>
                    <select class="border border-gray-300 rounded px-2 py-1 text-sm focus:ring-1 focus:ring-purple-600 focus:border-purple-600 bg-white">
                        <option value="10">10</option>
                    </select>
                    <label class="text-sm font-bold text-gray-700 whitespace-nowrap">entri</label>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label class="text-sm font-bold text-gray-700">Cari:</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="w-full sm:w-48 border border-gray-300 rounded px-3 py-1.5 text-sm focus:ring-1 focus:ring-purple-600 focus:border-purple-600">
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-indigo-900 text-white">
                        <th class="px-3 py-3 text-xs font-bold whitespace-nowrap border-r border-indigo-800 flex items-center gap-1 cursor-pointer">
                            Tanggal <svg class="w-3 h-3 text-indigo-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"></path></svg>
                        </th>
                        <th class="px-3 py-3 text-xs font-bold whitespace-nowrap border-r border-indigo-800 flex-1 min-w-[200px]">
                            <div class="flex items-center gap-1 cursor-pointer">
                                Nama Guru <svg class="w-3 h-3 text-indigo-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"></path></svg>
                            </div>
                        </th>
                        <th class="px-3 py-3 text-xs font-bold whitespace-nowrap border-r border-indigo-800">
                            <div class="flex items-center gap-1 cursor-pointer">
                                Rombel <svg class="w-3 h-3 text-indigo-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"></path></svg>
                            </div>
                        </th>
                        <th class="px-3 py-3 text-xs font-bold whitespace-nowrap border-r border-indigo-800">
                            <div class="flex items-center gap-1 cursor-pointer">
                                Mapel <svg class="w-3 h-3 text-indigo-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"></path></svg>
                            </div>
                        </th>
                        <th class="px-3 py-3 text-xs font-bold whitespace-nowrap border-r border-indigo-800 min-w-[150px]">
                            <div class="flex items-center gap-1 cursor-pointer">
                                KD / CP <svg class="w-3 h-3 text-indigo-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"></path></svg>
                            </div>
                        </th>
                        <th class="px-3 py-3 text-xs font-bold whitespace-nowrap border-r border-indigo-800 min-w-[150px]">
                            <div class="flex items-center gap-1 cursor-pointer">
                                Materi <svg class="w-3 h-3 text-indigo-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"></path></svg>
                            </div>
                        </th>
                        <th class="px-3 py-3 text-xs font-bold whitespace-nowrap border-r border-indigo-800 min-w-[150px]">
                            <div class="flex items-center gap-1 cursor-pointer">
                                Tugas / Kegiatan <svg class="w-3 h-3 text-indigo-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"></path></svg>
                            </div>
                        </th>
                        <th class="px-2 py-3 text-xs font-bold text-center border-r border-indigo-800">S</th>
                        <th class="px-2 py-3 text-xs font-bold text-center border-r border-indigo-800">I</th>
                        <th class="px-2 py-3 text-xs font-bold text-center border-r border-indigo-800">A</th>
                        <th class="px-3 py-3 text-xs font-bold whitespace-nowrap">
                            <div class="flex items-center gap-1 cursor-pointer">
                                Keterangan <svg class="w-3 h-3 text-indigo-300" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"></path></svg>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($jurnals as $index => $jurnal)
                    <tr class="hover:bg-gray-50 transition-colors {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50/50' }}">
                        <td class="px-3 py-3 text-sm text-gray-800 align-top whitespace-nowrap">
                            {{ $jurnal->tanggal->format('d/m/Y') }}
                        </td>
                        <td class="px-3 py-3 text-sm text-gray-800 align-top whitespace-nowrap font-medium uppercase cursor-pointer text-indigo-700 hover:underline" wire:click="showDetail('{{ json_encode($jurnal->jurnal_ids) }}')">
                            {{ $jurnal->guru->nama ?? '-' }}
                        </td>
                        <td class="px-3 py-3 text-sm text-gray-800 align-top whitespace-nowrap">
                            {{ $jurnal->kelas->nama ?? '-' }}
                        </td>
                        <td class="px-3 py-3 text-sm text-gray-800 align-top">
                            {{ $jurnal->mapel->nama ?? '-' }}
                        </td>
                        <td class="px-3 py-3 text-sm text-gray-800 align-top">
                            {{ $jurnal->kd_cp ?: '-' }}
                        </td>
                        <td class="px-3 py-3 text-sm text-gray-800 align-top">
                            {{ $jurnal->materi ?: '-' }}
                        </td>
                        <td class="px-3 py-3 text-sm text-gray-800 align-top">
                            {{ $jurnal->kegiatan ?: '-' }}
                        </td>
                        <td class="px-2 py-3 text-sm text-gray-800 align-top text-center">
                            {{ $jurnal->jumlah_sakit > 0 ? $jurnal->jumlah_sakit : '' }}
                        </td>
                        <td class="px-2 py-3 text-sm text-gray-800 align-top text-center">
                            {{ $jurnal->jumlah_izin > 0 ? $jurnal->jumlah_izin : '' }}
                        </td>
                        <td class="px-2 py-3 text-sm text-gray-800 align-top text-center">
                            {{ $jurnal->jumlah_alfa > 0 ? $jurnal->jumlah_alfa : '' }}
                        </td>
                        <td class="px-3 py-3 text-sm text-gray-800 align-top">
                            @php
                                $ket = [];
                                if ($jurnal->jumlah_hadir > 0) $ket[] = "Hadir " . $jurnal->jumlah_hadir;
                                if ($jurnal->jumlah_sakit > 0) $ket[] = "Sakit " . $jurnal->jumlah_sakit;
                                if ($jurnal->jumlah_izin > 0) $ket[] = "Izin " . $jurnal->jumlah_izin;
                                if ($jurnal->jumlah_alfa > 0) $ket[] = "Alfa " . $jurnal->jumlah_alfa;
                            @endphp
                            {{ implode(', ', $ket) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data jurnal ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <!-- Filter Dropdowns (Below Table) -->
                <tfoot class="border-t border-gray-200">
                    <tr class="bg-white">
                        <td class="p-2 border-r border-gray-200"></td>
                        <td class="p-2 border-r border-gray-200">
                            <select wire:model.live="filterGuru" class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs text-gray-600 focus:ring-1 focus:ring-purple-600 focus:border-purple-600">
                                <option value="">Nama Guru</option>
                                @foreach($gurus as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-2 border-r border-gray-200">
                            <select wire:model.live="filterKelas" class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs text-gray-600 focus:ring-1 focus:ring-purple-600 focus:border-purple-600">
                                <option value="">Rombel</option>
                                @foreach($kelasList as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-2 border-r border-gray-200">
                            <select wire:model.live="filterMapel" class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs text-gray-600 focus:ring-1 focus:ring-purple-600 focus:border-purple-600">
                                <option value="">Mapel</option>
                                @foreach($mapels as $m)
                                    <option value="{{ $m->id }}">{{ $m->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td colspan="7" class="p-2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-200 flex items-center justify-between text-sm text-gray-600">
            <div>
                Menampilkan {{ $jurnals->firstItem() ?? 0 }} sampai {{ $jurnals->lastItem() ?? 0 }} dari {{ $jurnals->total() }} entri
            </div>
            <div>
                {{ $jurnals->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    <!-- Detail Modal Alpine -->
    <div x-data="{ open: false }" 
         x-show="open" 
         @open-modal.window="if ($event.detail[0] === 'modal-detail-jurnal') open = true"
         @close-modal.window="if ($event.detail[0] === 'modal-detail-jurnal') open = false"
         class="fixed inset-0 z-50 flex items-center justify-center" 
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" x-show="open" x-transition.opacity @click="$wire.closeDetail()"></div>
        
        <!-- Modal Content -->
        <div class="relative bg-white rounded-lg shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col m-4" 
             x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95">
            
            @if($detailJurnal)
            <div class="p-6 border-b border-gray-200 flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Detail Jurnal Mengajar</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $detailJurnal->tanggal->format('l, d F Y') }} &middot; 
                        Jam ke-{{ $detailJurnal->jam_mulai == $detailJurnal->jam_selesai ? $detailJurnal->jam_mulai : $detailJurnal->jam_mulai . '-' . $detailJurnal->jam_selesai }} 
                        <span class="font-bold text-indigo-600 ml-1">({{ $detailJurnal->total_jp }} JP)</span>
                    </p>
                </div>
                <button @click="$wire.closeDetail()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1 space-y-6">
                <!-- Info Guru & Kelas -->
                <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Guru Pengajar</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $detailJurnal->guru->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Mata Pelajaran</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $detailJurnal->mapel->nama ?? '-' }} ({{ $detailJurnal->kelas->nama ?? '-' }})</p>
                    </div>
                </div>

                <!-- Konten Jurnal -->
                @if($detailJurnal->kd_cp)
                <div>
                    <h4 class="text-sm font-bold text-indigo-700 flex items-center gap-2 mb-2">
                        KD / CP
                    </h4>
                    <div class="bg-white p-4 rounded-lg border border-gray-200 text-sm text-gray-800 leading-relaxed">
                        {!! nl2br(e($detailJurnal->kd_cp)) !!}
                    </div>
                </div>
                @endif

                <div>
                    <h4 class="text-sm font-bold text-indigo-700 flex items-center gap-2 mb-2">
                        Materi Pembelajaran
                    </h4>
                    <div class="bg-white p-4 rounded-lg border border-gray-200 text-sm text-gray-800 leading-relaxed">
                        {!! nl2br(e($detailJurnal->materi ?: 'Belum diisi')) !!}
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-indigo-700 flex items-center gap-2 mb-2">
                        Tugas / Kegiatan
                    </h4>
                    <div class="bg-white p-4 rounded-lg border border-gray-200 text-sm text-gray-800 leading-relaxed">
                        {!! nl2br(e($detailJurnal->kegiatan ?: 'Belum diisi')) !!}
                    </div>
                </div>

                <!-- Dokumentasi -->
                @if($detailJurnal->dokumentasi && is_array($detailJurnal->dokumentasi))
                <div>
                    <h4 class="text-sm font-bold text-indigo-700 flex items-center gap-2 mb-3">
                        Dokumentasi
                    </h4>
                    <div class="flex flex-wrap gap-3">
                        @foreach($detailJurnal->dokumentasi as $fotoPath)
                            <a href="{{ Storage::url($fotoPath) }}" target="_blank" class="block w-24 h-24 sm:w-32 sm:h-32 rounded-lg overflow-hidden border border-gray-200 group relative">
                                <img src="{{ Storage::url($fotoPath) }}" alt="Dokumentasi" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="p-10 flex items-center justify-center">
                <svg class="animate-spin w-8 h-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>
            @endif
        </div>
    </div>
</div>
