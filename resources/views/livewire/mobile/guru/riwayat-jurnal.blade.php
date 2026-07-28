<div class="space-y-6 animate-fade-in pb-10">
    <div class="mt-4 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Riwayat Jurnal</h2>
        <a href="{{ route('guru.jurnal.cetak', ['bulan' => $filterBulan, 'tahun' => $filterTahun]) }}" target="_blank" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-xl font-semibold text-xs flex items-center gap-1.5 hover:bg-indigo-600 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak PDF
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-100 space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Bulan</label>
                <select wire:model.live="filterBulan" class="w-full bg-slate-50 border-transparent focus:bg-white focus:border-indigo-500 rounded-xl text-sm p-2">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ \Carbon\Carbon::create()->month($i)->locale('id')->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tahun</label>
                <select wire:model.live="filterTahun" class="w-full bg-slate-50 border-transparent focus:bg-white focus:border-indigo-500 rounded-xl text-sm p-2">
                    @php $currentYear = date('Y'); @endphp
                    @for($i = $currentYear; $i >= $currentYear - 2; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kelas (Opsional)</label>
                <select wire:model.live="filterKelas" class="w-full bg-slate-50 border-transparent focus:bg-white focus:border-indigo-500 rounded-xl text-sm p-2">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Mapel (Opsional)</label>
                <select wire:model.live="filterMapel" class="w-full bg-slate-50 border-transparent focus:bg-white focus:border-indigo-500 rounded-xl text-sm p-2">
                    <option value="">Semua Mapel</option>
                    @foreach($mapelList as $mapel)
                        <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Timeline / List -->
    <div class="space-y-4">
        @forelse($jurnals as $jurnal)
            <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-100 flex flex-col gap-3">
                <div class="flex items-center justify-between border-b border-slate-50 pb-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600">
                            {{ \Carbon\Carbon::parse($jurnal->tanggal)->locale('id')->isoFormat('D MMM YYYY') }}
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-600 uppercase">
                            Jam ke-{{ $jurnal->jam_ke }}
                        </span>
                    </div>
                    <span class="text-xs font-bold text-emerald-500">Tersimpan</span>
                </div>
                
                <div>
                    <h4 class="font-bold text-slate-800">{{ $jurnal->mapel->nama_mapel ?? '-' }}</h4>
                    <p class="text-xs text-slate-500 font-medium">Kelas {{ $jurnal->kelas->nama ?? '-' }}</p>
                </div>
                
                <div class="bg-slate-50 rounded-2xl p-3 text-sm text-slate-700">
                    <p class="font-semibold text-xs text-slate-400 mb-0.5">Materi</p>
                    <p class="mb-2">{{ $jurnal->materi }}</p>
                    
                    <p class="font-semibold text-xs text-slate-400 mb-0.5">Kegiatan</p>
                    <p>{{ $jurnal->kegiatan }}</p>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-8 border border-slate-100 flex flex-col items-center justify-center text-center shadow-sm">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg>
                </div>
                <h4 class="font-bold text-slate-700">Belum Ada Jurnal</h4>
                <p class="text-sm text-slate-500 mt-1">Tidak ditemukan jurnal pada periode ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $jurnals->links('pagination::tailwind') }}
    </div>
</div>
