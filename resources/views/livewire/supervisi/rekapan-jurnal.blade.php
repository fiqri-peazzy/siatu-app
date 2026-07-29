<div>
    <x-slot name="header">
        Rekapan Jurnal Mengajar
    </x-slot>

    <div class="bg-surface rounded-2xl shadow-sm border border-surface-container-high overflow-hidden">
        
        <!-- Header & Toolbar -->
        <div class="p-6 border-b border-surface-container-high flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-on-surface">Data Jurnal Guru</h3>
                <p class="text-sm text-on-surface-variant mt-1">Laporan rekapitulasi jurnal mengajar yang telah diisi oleh guru.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('supervisi.jurnal.export', ['bulan' => $filterBulan, 'tahun' => $filterTahun]) }}" target="_blank" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-xl shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export Excel
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="p-6 bg-surface-container/30 border-b border-surface-container-high">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Pencarian</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-on-surface-variant/50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" class="w-full pl-10 pr-4 py-2.5 bg-white border border-line-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" placeholder="Cari materi, nama guru, mapel...">
                    </div>
                </div>

                <!-- Guru -->
                <div>
                    <label class="block text-[11px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Filter Guru</label>
                    <select wire:model.live="filterGuru" class="w-full px-4 py-2.5 bg-white border border-line-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors cursor-pointer">
                        <option value="">Semua Guru</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}">{{ $g->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Bulan -->
                <div>
                    <label class="block text-[11px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Bulan</label>
                    <select wire:model.live="filterBulan" class="w-full px-4 py-2.5 bg-white border border-line-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors cursor-pointer">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Tahun -->
                <div>
                    <label class="block text-[11px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Tahun</label>
                    <select wire:model.live="filterTahun" class="w-full px-4 py-2.5 bg-white border border-line-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors cursor-pointer">
                        @for($i = date('Y') - 2; $i <= date('Y'); $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container/50 border-b border-surface-container-high">
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Guru</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Kelas/Mapel</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-center">Jam Ke</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-high">
                    @forelse($jurnals as $jurnal)
                    <tr class="hover:bg-surface-container/20 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-on-surface">{{ $jurnal->tanggal->format('d M Y') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">
                                    {{ substr($jurnal->guru->nama ?? 'A', 0, 1) }}
                                </div>
                                <span class="text-sm font-semibold text-on-surface">{{ $jurnal->guru->nama ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-on-surface">{{ $jurnal->kelas->nama ?? '-' }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $jurnal->mapel->nama ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($jurnal->jam_mulai == $jurnal->jam_selesai)
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-surface-container text-on-surface-variant font-bold text-xs">{{ $jurnal->jam_mulai }}</span>
                            @else
                                <span class="inline-flex items-center justify-center px-2 h-6 rounded-md bg-surface-container text-on-surface-variant font-bold text-xs">{{ $jurnal->jam_mulai }}-{{ $jurnal->jam_selesai }}</span>
                            @endif
                            <div class="text-[10px] font-semibold text-primary mt-1">{{ $jurnal->total_jp }} JP</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($jurnal->status == 'diisi')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700">Diisi</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-700">Kosong</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <button wire:click="showDetail('{{ json_encode($jurnal->jurnal_ids) }}')" class="inline-flex items-center justify-center p-2 rounded-lg text-primary hover:bg-primary/10 transition-colors" title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-on-surface-variant">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                <p class="text-base font-semibold text-slate-700">Data Jurnal Tidak Ditemukan</p>
                                <p class="text-sm mt-1">Coba sesuaikan filter pencarian untuk melihat data.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-surface-container-high bg-surface-container/30">
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
            <div class="p-6 border-b border-surface-container-high flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold text-on-surface">Detail Jurnal Mengajar</h3>
                    <p class="text-sm text-on-surface-variant mt-1">{{ $detailJurnal->tanggal->format('l, d F Y') }} &middot; 
                        Jam ke-{{ $detailJurnal->jam_mulai == $detailJurnal->jam_selesai ? $detailJurnal->jam_mulai : $detailJurnal->jam_mulai . '-' . $detailJurnal->jam_selesai }} 
                        <span class="font-bold text-primary ml-1">({{ $detailJurnal->total_jp }} JP)</span>
                    </p>
                </div>
                <button @click="$wire.closeDetail()" class="w-8 h-8 flex items-center justify-center rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                <!-- Info Guru & Kelas -->
                <div class="grid grid-cols-2 gap-4 bg-surface-container/30 p-4 rounded-xl border border-surface-container-high">
                    <div>
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1">Guru Pengajar</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $detailJurnal->guru->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1">Mata Pelajaran</p>
                        <p class="text-sm font-semibold text-on-surface">{{ $detailJurnal->mapel->nama ?? '-' }} ({{ $detailJurnal->kelas->nama ?? '-' }})</p>
                    </div>
                </div>

                <!-- Konten Jurnal -->
                <div>
                    <h4 class="text-sm font-bold text-primary flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        Materi Pembelajaran
                    </h4>
                    <div class="bg-white p-4 rounded-xl border border-line-200 text-sm text-on-surface leading-relaxed">
                        {!! nl2br(e($detailJurnal->materi ?: 'Belum diisi')) !!}
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-primary flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                        Kegiatan Pembelajaran
                    </h4>
                    <div class="bg-white p-4 rounded-xl border border-line-200 text-sm text-on-surface leading-relaxed">
                        {!! nl2br(e($detailJurnal->kegiatan ?: 'Belum diisi')) !!}
                    </div>
                </div>

                <!-- Dokumentasi -->
                @if($detailJurnal->dokumentasi && is_array($detailJurnal->dokumentasi))
                <div>
                    <h4 class="text-sm font-bold text-primary flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        Dokumentasi
                    </h4>
                    <div class="flex flex-wrap gap-3">
                        @foreach($detailJurnal->dokumentasi as $fotoPath)
                            <a href="{{ Storage::url($fotoPath) }}" target="_blank" class="block w-24 h-24 sm:w-32 sm:h-32 rounded-xl overflow-hidden border border-line-200 group relative">
                                <img src="{{ Storage::url($fotoPath) }}" alt="Dokumentasi" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" /></svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            
            <div class="p-6 border-t border-surface-container-high bg-surface-container/30 flex justify-end">
                <button @click="$wire.closeDetail()" class="px-5 py-2.5 bg-white border border-line-200 text-on-surface font-semibold rounded-xl hover:bg-surface-container transition-colors shadow-sm">
                    Tutup
                </button>
            </div>
            @else
            <div class="p-10 flex items-center justify-center">
                <svg class="animate-spin w-8 h-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>
            @endif
        </div>
    </div>
</div>
