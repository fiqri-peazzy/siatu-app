<div>
    <x-slot:header>Rekapan Jurnal Mengajar</x-slot:header>

    <div class="bg-surface rounded-xl shadow-sm border border-outline-variant p-6 mb-6">
        
        <!-- Header & Toolbar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
            <h3 class="text-lg font-bold text-on-surface">Jurnal Mengajar</h3>
            
            <div class="flex flex-col sm:flex-row items-center gap-2">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-bold text-on-surface-variant">Tanggal</label>
                    <input type="date" wire:model.live="tanggalMulai" class="border border-outline-variant rounded-lg px-3 py-1.5 text-sm bg-surface text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                    <span class="text-sm font-bold text-on-surface-variant">s/d</span>
                    <input type="date" wire:model.live="tanggalSelesai" class="border border-outline-variant rounded-lg px-3 py-1.5 text-sm bg-surface text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                </div>
                <a href="{{ route('supervisi.jurnal.export', ['start_date' => $tanggalMulai, 'end_date' => $tanggalSelesai, 'guru' => $filterGuru, 'mapel' => $filterMapel, 'kelas' => $filterKelas]) }}" target="_blank" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-lg flex items-center gap-2 shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Eksport XLS
                </a>
            </div>
        </div>

        <!-- Filters (2nd Row) -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-4">
            <div>
                <select wire:model.live="filterGuru" class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                    <option value="">Semua Guru</option>
                    @foreach($gurus as $g)
                        <option value="{{ $g->id }}">{{ $g->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="filterKelas" class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                    <option value="">Semua Rombel</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="filterMapel" class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                    <option value="">Semua Mapel</option>
                    @foreach($mapels as $m)
                        <option value="{{ $m->id }}">{{ $m->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari jurnal..." class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface text-on-surface focus:border-primary focus:ring-1 focus:ring-primary outline-none">
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-xl border border-outline-variant">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-high text-on-surface text-xs font-bold uppercase tracking-wider">
                        <th class="px-4 py-4 whitespace-nowrap border-b border-outline-variant">Tanggal</th>
                        <th class="px-4 py-4 whitespace-nowrap border-b border-outline-variant min-w-[200px]">Nama Guru</th>
                        <th class="px-4 py-4 whitespace-nowrap border-b border-outline-variant">Rombel</th>
                        <th class="px-4 py-4 whitespace-nowrap border-b border-outline-variant">Mapel</th>
                        <th class="px-4 py-4 whitespace-nowrap border-b border-outline-variant min-w-[150px]">KD / CP</th>
                        <th class="px-4 py-4 whitespace-nowrap border-b border-outline-variant min-w-[150px]">Materi</th>
                        <th class="px-4 py-4 whitespace-nowrap border-b border-outline-variant min-w-[150px]">Kegiatan</th>
                        <th class="px-2 py-4 whitespace-nowrap border-b border-outline-variant text-center" title="Sakit">S</th>
                        <th class="px-2 py-4 whitespace-nowrap border-b border-outline-variant text-center" title="Izin">I</th>
                        <th class="px-2 py-4 whitespace-nowrap border-b border-outline-variant text-center" title="Alfa">A</th>
                        <th class="px-4 py-4 whitespace-nowrap border-b border-outline-variant">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($jurnals as $index => $jurnal)
                    <tr class="hover:bg-surface-container-lowest transition-colors bg-surface">
                        <td class="px-4 py-3 text-sm text-on-surface align-top whitespace-nowrap">
                            {{ $jurnal->tanggal->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-primary align-top font-bold cursor-pointer hover:underline" wire:click="showDetail('{{ json_encode($jurnal->jurnal_ids) }}')">
                            {{ $jurnal->guru->nama ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-on-surface-variant align-top whitespace-nowrap">
                            {{ $jurnal->kelas->nama ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-on-surface-variant align-top">
                            {{ $jurnal->mapel->nama ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-on-surface align-top">
                            {{ $jurnal->kd_cp ?: '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-on-surface align-top">
                            {{ $jurnal->materi ?: '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-on-surface align-top">
                            {{ $jurnal->kegiatan ?: '-' }}
                        </td>
                        <td class="px-2 py-3 text-sm text-on-surface align-top text-center">
                            {{ $jurnal->jumlah_sakit > 0 ? $jurnal->jumlah_sakit : '' }}
                        </td>
                        <td class="px-2 py-3 text-sm text-on-surface align-top text-center">
                            {{ $jurnal->jumlah_izin > 0 ? $jurnal->jumlah_izin : '' }}
                        </td>
                        <td class="px-2 py-3 text-sm text-on-surface align-top text-center">
                            {{ $jurnal->jumlah_alfa > 0 ? $jurnal->jumlah_alfa : '' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-on-surface-variant align-top">
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
                        <td colspan="11" class="px-6 py-12 text-center text-on-surface-variant">
                            Tidak ada data jurnal ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-6">
            {{ $jurnals->links('pagination::tailwind') }}
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
        <div class="relative bg-surface rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col m-4" 
             x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95">
            
            @if($detailJurnal)
            <div class="p-6 border-b border-outline-variant flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold text-on-surface">Detail Jurnal Mengajar</h3>
                    <p class="text-sm text-on-surface-variant mt-1">{{ $detailJurnal->tanggal->format('l, d F Y') }} &middot; 
                        Jam ke-{{ $detailJurnal->jam_mulai == $detailJurnal->jam_selesai ? $detailJurnal->jam_mulai : $detailJurnal->jam_mulai . '-' . $detailJurnal->jam_selesai }} 
                        <span class="font-bold text-primary ml-1">({{ $detailJurnal->total_jp }} JP)</span>
                    </p>
                </div>
                <button @click="$wire.closeDetail()" class="w-8 h-8 flex items-center justify-center rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface-variant transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1 space-y-6">
                <!-- Info Guru & Kelas -->
                <div class="grid grid-cols-2 gap-4 bg-surface-container-lowest p-4 rounded-xl border border-outline-variant">
                    <div>
                        <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Guru Pengajar</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $detailJurnal->guru->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Mata Pelajaran</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $detailJurnal->mapel->nama ?? '-' }} ({{ $detailJurnal->kelas->nama ?? '-' }})</p>
                    </div>
                </div>

                <!-- Konten Jurnal -->
                @if($detailJurnal->kd_cp)
                <div>
                    <h4 class="text-sm font-bold text-primary flex items-center gap-2 mb-2">
                        KD / CP
                    </h4>
                    <div class="bg-surface p-4 rounded-xl border border-outline-variant text-sm text-on-surface leading-relaxed">
                        {!! nl2br(e($detailJurnal->kd_cp)) !!}
                    </div>
                </div>
                @endif

                <div>
                    <h4 class="text-sm font-bold text-primary flex items-center gap-2 mb-2">
                        Materi Pembelajaran
                    </h4>
                    <div class="bg-surface p-4 rounded-xl border border-outline-variant text-sm text-on-surface leading-relaxed">
                        {!! nl2br(e($detailJurnal->materi ?: 'Belum diisi')) !!}
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-primary flex items-center gap-2 mb-2">
                        Tugas / Kegiatan
                    </h4>
                    <div class="bg-surface p-4 rounded-xl border border-outline-variant text-sm text-on-surface leading-relaxed">
                        {!! nl2br(e($detailJurnal->kegiatan ?: 'Belum diisi')) !!}
                    </div>
                </div>

                <!-- Dokumentasi -->
                @if($detailJurnal->dokumentasi && is_array($detailJurnal->dokumentasi))
                <div>
                    <h4 class="text-sm font-bold text-primary flex items-center gap-2 mb-3">
                        Dokumentasi
                    </h4>
                    <div class="flex flex-wrap gap-3">
                        @foreach($detailJurnal->dokumentasi as $fotoPath)
                            <a href="{{ Storage::url($fotoPath) }}" target="_blank" class="block w-24 h-24 sm:w-32 sm:h-32 rounded-xl overflow-hidden border border-outline-variant group relative">
                                <img src="{{ Storage::url($fotoPath) }}" alt="Dokumentasi" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="p-10 flex items-center justify-center">
                <svg class="animate-spin w-8 h-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>
            @endif
        </div>
    </div>
</div>
