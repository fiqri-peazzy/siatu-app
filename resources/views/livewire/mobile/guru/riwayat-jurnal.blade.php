<div class="space-y-6 pb-12">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between mt-2">
        <h2 class="text-2xl font-extrabold text-ink-900 tracking-tight">Riwayat Jurnal</h2>
        <a href="{{ route('guru.jurnal.cetak', ['bulan' => $filterBulan, 'tahun' => $filterTahun]) }}"
           target="_blank"
           class="flex items-center gap-1.5 px-3 py-1.5 bg-navy-deep text-white rounded-xl text-[11px] font-bold hover:bg-navy-mid transition-colors active:scale-95">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak PDF
        </a>
    </div>

    {{-- ── Filter Panel ── --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-line-200 space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Bulan</label>
                <select wire:model.live="filterBulan"
                        class="w-full bg-paper border border-line-200 focus:bg-white focus:border-navy-mid focus:ring-2 focus:ring-navy-mid/20 rounded-xl text-sm p-2.5 outline-none transition-all">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                            {{ \Carbon\Carbon::create()->month($i)->locale('id')->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tahun</label>
                <select wire:model.live="filterTahun"
                        class="w-full bg-paper border border-line-200 focus:bg-white focus:border-navy-mid focus:ring-2 focus:ring-navy-mid/20 rounded-xl text-sm p-2.5 outline-none transition-all">
                    @php $currentYear = date('Y'); @endphp
                    @for($i = $currentYear; $i >= $currentYear - 2; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Kelas</label>
                <select wire:model.live="filterKelas"
                        class="w-full bg-paper border border-line-200 focus:bg-white focus:border-navy-mid focus:ring-2 focus:ring-navy-mid/20 rounded-xl text-sm p-2.5 outline-none transition-all">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Mapel</label>
                <select wire:model.live="filterMapel"
                        class="w-full bg-paper border border-line-200 focus:bg-white focus:border-navy-mid focus:ring-2 focus:ring-navy-mid/20 rounded-xl text-sm p-2.5 outline-none transition-all">
                    <option value="">Semua Mapel</option>
                    @foreach($mapelList as $mapel)
                        <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ── Timeline List ── --}}
    <div class="relative pl-3 space-y-5
                before:absolute before:inset-0 before:ml-[1.35rem] before:-translate-x-px
                before:h-full before:w-0.5 before:bg-line-200">

        @forelse($jurnals as $jurnal)
            <div class="relative flex items-start gap-4">
                {{-- Dot: filled = sudah diisi --}}
                <div class="absolute left-0 w-5 h-5 flex items-center justify-center bg-paper">
                    <div class="w-3.5 h-3.5 bg-success-teal rounded-full ring-4 ring-paper z-10"></div>
                </div>

                <div class="flex-1 ml-6 bg-white rounded-2xl p-4 shadow-sm border border-line-200">
                    {{-- Top meta --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-bold text-ink-900">
                                {{ \Carbon\Carbon::parse($jurnal->tanggal)->locale('id')->isoFormat('D MMM YYYY') }}
                            </span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded border border-signal-blue/20 bg-signal-blue/5 text-signal-blue uppercase tracking-wider">
                                Jam ke-{{ $jurnal->jam_ke }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1 text-[10px] font-bold text-success-teal">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Tersimpan
                        </div>
                    </div>

                    {{-- Mapel & Kelas --}}
                    <h4 class="font-bold text-ink-900 text-[15px] leading-tight">{{ $jurnal->mapel->nama ?? '-' }}</h4>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5 mb-3">Kelas {{ $jurnal->kelas->nama ?? '-' }}</p>

                    {{-- Content --}}
                    <div class="bg-paper rounded-xl p-3 space-y-2 border border-line-200">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Materi</p>
                            <p class="text-sm text-ink-900 leading-relaxed">{{ $jurnal->materi }}</p>
                        </div>
                        <div class="border-t border-line-200 pt-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Kegiatan</p>
                            <p class="text-sm text-ink-900 leading-relaxed">{{ $jurnal->kegiatan }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-8 border border-line-200 flex flex-col items-center justify-center text-center shadow-sm ml-6">
                <div class="w-16 h-16 bg-paper rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <h4 class="font-bold text-ink-900">Belum Ada Jurnal</h4>
                <p class="text-sm text-slate-500 mt-1">Tidak ditemukan jurnal pada periode ini.</p>
            </div>
        @endforelse
    </div>

    {{-- ── Pagination ── --}}
    <div class="mt-4">
        {{ $jurnals->links('pagination::tailwind') }}
    </div>
</div>
