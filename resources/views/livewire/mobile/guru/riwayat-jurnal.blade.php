<div class="space-y-6 pb-24 bg-slate-50 min-h-screen">

    {{-- ── Header ── --}}
    <div class="px-5 pt-6 pb-2">
        <h2 class="text-[22px] font-extrabold text-[#121A2F] tracking-tight">Riwayat Jurnal</h2>
        <p class="text-sm text-slate-500 mt-1">Lacak aktivitas pembelajaran terdahulu.</p>
    </div>

    {{-- ── Filter Panel ── --}}
    <div class="px-5 flex gap-3">
        {{-- Bulan --}}
        <div class="relative bg-white border border-slate-200 rounded-xl px-4 py-2 flex-1 shadow-sm">
            <select wire:model.live="filterBulan" class="w-full bg-transparent text-sm font-semibold text-slate-700 appearance-none outline-none z-10 relative">
                @for($i=1; $i<=12; $i++)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                        {{ \Carbon\Carbon::create()->month($i)->locale('id')->isoFormat('MMMM') }} {{ $filterTahun }}
                    </option>
                @endfor
            </select>
            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </div>
        </div>

        {{-- Kelas --}}
        <div class="relative bg-white border border-slate-200 rounded-xl px-4 py-2 flex-1 shadow-sm">
            <select wire:model.live="filterKelas" class="w-full bg-transparent text-sm font-semibold text-slate-700 appearance-none outline-none z-10 relative">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $kelas)
                    <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                @endforeach
            </select>
            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
            </div>
        </div>
    </div>

    {{-- ── Timeline List ── --}}
    <div class="relative px-5">
        {{-- Vertical Line --}}
        <div class="absolute top-2 bottom-0 left-7 w-[2px] bg-[#121A2F]"></div>

        <div class="space-y-6 relative z-10">
            @forelse($jurnals as $jurnal)
                <div class="relative pl-6">
                    {{-- Dot --}}
                    <div class="absolute left-[-21px] top-1.5 w-2.5 h-2.5 rounded-full ring-[3px] ring-slate-50
                                {{ $jurnal->status == 'diisi' ? 'bg-[#3B65F3]' : 'bg-amber-500' }}"></div>

                    {{-- Date & Time --}}
                    <div class="mb-2">
                        <h4 class="text-[11px] font-bold text-[#3B65F3] uppercase tracking-widest">
                            {{ \Carbon\Carbon::parse($jurnal->tanggal)->locale('id')->isoFormat('dddd, D MMM YYYY') }}
                        </h4>
                        <p class="text-[10px] font-semibold text-slate-400 font-mono mt-0.5">07:30 - 09:00</p>
                    </div>

                    {{-- Card --}}
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
                        <div class="flex items-start justify-between mb-1">
                            <h3 class="font-semibold text-[15px] text-slate-800 tracking-tight">{{ $jurnal->kelas->nama ?? '-' }}</h3>
                            @if($jurnal->status == 'diisi')
                                <span class="bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider">Terisi</span>
                            @else
                                <span class="bg-amber-50 text-amber-600 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider">Draft</span>
                            @endif
                        </div>
                        <p class="text-[11px] font-medium text-slate-500 mb-3">{{ $jurnal->mapel->nama ?? '-' }}</p>
                        
                        @if($jurnal->status == 'diisi')
                            <p class="text-sm text-slate-700 leading-relaxed">{{ Str::limit($jurnal->materi, 80) }}</p>
                        @else
                            <p class="text-sm text-slate-500 italic leading-relaxed">Belum ada ringkasan materi yang disimpan.</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-8 border border-slate-200 flex flex-col items-center justify-center text-center shadow-sm ml-6 relative z-10">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-800">Belum Ada Jurnal</h4>
                    <p class="text-sm text-slate-500 mt-1">Tidak ditemukan jurnal pada periode ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination (if needed) --}}
    <div class="px-5 mt-6">
        {{ $jurnals->links('pagination::tailwind') }}
    </div>

    {{-- ── Fixed Bottom Button ── --}}
    <div class="fixed bottom-16 left-0 right-0 p-4 max-w-md mx-auto z-40 bg-gradient-to-t from-slate-50 via-slate-50 to-transparent pt-10">
        <a href="{{ route('guru.jurnal.cetak', ['bulan' => $filterBulan, 'tahun' => $filterTahun]) }}" target="_blank"
           class="w-full bg-[#121A2F] text-white font-bold py-3.5 rounded-2xl shadow-lg hover:bg-slate-800 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Unduh PDF Bulan Ini
        </a>
    </div>

</div>
