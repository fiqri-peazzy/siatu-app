<div class="space-y-6 pb-24 bg-slate-50 min-h-screen">

    {{-- ── Header ── --}}
    <div class="px-5 pt-6 pb-2">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ \Carbon\Carbon::parse($tanggalAktif)->locale('id')->isoFormat('dddd, D MMM YYYY') }}</p>
        <h2 class="text-[22px] font-extrabold text-[#121A2F] tracking-tight">Jadwal Mengajar</h2>
    </div>

    {{-- ── Tabs ── --}}
    <div class="px-5">
        <div class="bg-white rounded-xl p-1 flex shadow-sm border border-slate-100">
            <button wire:click="setMode('harian')"
                    class="flex-1 py-2 text-[13px] font-bold rounded-lg transition-all
                           {{ $mode === 'harian' ? 'bg-[#3B65F3] text-white shadow-md' : 'text-slate-400 hover:text-slate-600' }}">
                Harian
            </button>
            <button wire:click="setMode('mingguan')"
                    class="flex-1 py-2 text-[13px] font-bold rounded-lg transition-all
                           {{ $mode === 'mingguan' ? 'bg-[#3B65F3] text-white shadow-md' : 'text-slate-400 hover:text-slate-600' }}">
                Mingguan
            </button>
        </div>
    </div>

    @if($mode === 'harian')
        {{-- ── Sesi Aktif ── --}}
        @if($sesiAktif)
        <div class="px-5">
            <div class="bg-[#121A2F] rounded-2xl p-5 shadow-lg relative overflow-hidden flex items-center justify-between">
                <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                <div class="relative z-10">
                    <p class="text-[10px] text-sky-300 font-bold uppercase tracking-wider mb-1">Sesi Aktif</p>
                    <h3 class="text-xl font-extrabold text-white mb-1">{{ $sesiAktif->mapel->nama ?? '-' }}</h3>
                    <p class="text-[11px] font-medium text-slate-300 font-mono">
                        {{ substr($sesiAktif->jam_mulai, 0, 5) }} - {{ substr($sesiAktif->jam_selesai, 0, 5) }} (Sesi {{ $sesiAktif->jam_ke }})
                    </p>
                </div>
                <div class="relative z-10 w-12 h-12 rounded-full bg-[#3B65F3]/20 flex items-center justify-center border border-[#3B65F3]/30">
                    <svg class="w-6 h-6 text-[#3B65F3]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        @endif

        {{-- ── Timeline ── --}}
        <div class="relative px-5 mt-8">
            <div class="absolute top-2 bottom-0 left-7 w-[2px] bg-slate-200"></div>

            <div class="space-y-6 relative z-10">
                @forelse($jadwals as $jadwal)
                    <div class="relative pl-6">
                        {{-- Dot Status --}}
                        @if($jadwal->status_jurnal === 'sudah_diisi')
                            <div class="absolute left-[-21px] top-1.5 w-3 h-3 rounded-full bg-[#3B65F3] ring-[3px] ring-slate-50 flex items-center justify-center">
                                <svg class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        @elseif($jadwal->status_jurnal === 'belum_diisi')
                            <div class="absolute left-[-21px] top-1.5 w-3 h-3 rounded-full border-2 border-[#3B65F3] bg-white ring-[3px] ring-slate-50 flex items-center justify-center">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#3B65F3]"></div>
                            </div>
                        @else
                            <div class="absolute left-[-21px] top-1.5 w-3 h-3 rounded-full border-2 border-amber-500 bg-white ring-[3px] ring-slate-50"></div>
                        @endif

                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 {{ $jadwal->status_jurnal === 'belum_diisi' ? 'ring-1 ring-[#3B65F3] shadow-md' : '' }}">
                            <div class="flex items-start justify-between mb-3">
                                <span class="text-[11px] font-bold text-slate-500 font-mono bg-slate-50 px-2 py-0.5 rounded border border-slate-100">
                                    {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                </span>
                                @if($jadwal->status_jurnal === 'sudah_diisi')
                                    <span class="bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider">Sudah Diisi</span>
                                @elseif($jadwal->status_jurnal === 'belum_diisi')
                                    <span class="bg-amber-50 text-amber-600 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider">Belum Diisi</span>
                                @else
                                    <span class="bg-slate-100 text-slate-500 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider">Mendatang</span>
                                @endif
                            </div>

                            <h3 class="font-extrabold text-[15px] text-[#121A2F]">{{ $jadwal->kelas->nama ?? '-' }}</h3>
                            <p class="text-[11px] font-medium text-slate-500 mb-4">{{ $jadwal->mapel->nama ?? '-' }}</p>

                            @if($jadwal->status_jurnal === 'sudah_diisi')
                                <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Jurnal Pelajaran #JRN-{{ \Carbon\Carbon::parse($jadwal->jurnal_ref->tanggal)->format('Ymd') }}-{{ str_pad($jadwal->jurnal_ref->id, 2, '0', STR_PAD_LEFT) }}
                                </div>
                            @elseif($jadwal->status_jurnal === 'belum_diisi')
                                <a href="{{ route('guru.jurnal.form', implode(',', $jadwal->jadwal_ids)) }}" class="block w-full bg-[#3B65F3] text-white text-center py-2.5 rounded-xl text-xs font-bold hover:bg-blue-600 transition-colors flex items-center justify-center gap-1">
                                    Isi Jurnal Pelajaran
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            @else
                                <div class="w-full bg-slate-50 border border-slate-100 text-slate-400 text-center py-2.5 rounded-xl text-xs font-bold cursor-not-allowed">
                                    Belum Dapat Diisi
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="font-bold text-slate-800">Tidak ada jadwal</h4>
                        <p class="text-sm text-slate-500 mt-1">Anda tidak memiliki jadwal mengajar hari ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @else
        {{-- ── Mingguan ── --}}
        <div class="px-5 mt-6 space-y-6">
            @forelse($jadwals as $hari => $listJadwal)
                <div>
                    <h3 class="text-sm font-bold text-[#121A2F] uppercase tracking-wider mb-3 bg-white px-3 py-1.5 rounded-lg border border-slate-200 inline-block">{{ $hari }}</h3>
                    <div class="space-y-3">
                        @foreach($listJadwal as $jadwal)
                            <div class="bg-white rounded-xl p-3 shadow-sm border border-slate-100 flex gap-3">
                                <div class="w-1.5 bg-[#3B65F3] rounded-full shrink-0"></div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1">
                                        <h4 class="font-bold text-sm text-[#121A2F]">{{ $jadwal->kelas->nama ?? '-' }}</h4>
                                        <span class="text-[10px] font-bold text-slate-500 font-mono">{{ substr($jadwal->jam_mulai, 0, 5) }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500">{{ $jadwal->mapel->nama ?? '-' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <p class="text-sm text-slate-500">Tidak ada jadwal minggu ini.</p>
                </div>
            @endforelse
        </div>
    @endif

    {{-- Floating Action Button (FAB) --}}
    <a href="#" class="fixed bottom-24 right-5 w-14 h-14 bg-[#121A2F] text-white rounded-full shadow-xl flex items-center justify-center hover:bg-slate-800 active:scale-90 transition-all z-40 border border-white/10">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
    </a>
</div>
