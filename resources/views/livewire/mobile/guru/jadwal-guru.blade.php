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

        {{-- ── Daftar Jadwal (Card Stack) ── --}}
        <div class="px-5 mt-8">
            <div class="space-y-4">
                @forelse($jadwals as $jadwal)
                    <div class="bg-white rounded-[16px] p-4 shadow-sm ring-1 {{ $jadwal->status_jurnal === 'sudah_diisi' ? 'ring-slate-100' : ($jadwal->status_jurnal === 'belum_diisi' ? 'ring-[#FCD34D] shadow-[#FEF3C7]/50' : 'ring-slate-200 opacity-70') }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full {{ $jadwal->status_jurnal === 'sudah_diisi' ? 'bg-[#059669]' : ($jadwal->status_jurnal === 'belum_diisi' ? 'bg-[#D97706]' : 'bg-slate-400') }}"></div>
                                <span class="font-mono text-sm font-bold text-slate-700 tracking-tight">
                                    {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                </span>
                            </div>
                            @if($jadwal->status_jurnal === 'sudah_diisi')
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-md bg-[#ECFDF5] text-[#059669] uppercase tracking-wider">
                                    Sudah Diisi
                                </span>
                            @elseif($jadwal->status_jurnal === 'belum_diisi')
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-md bg-[#FFFBEB] text-[#D97706] uppercase tracking-wider">
                                    Belum Diisi
                                </span>
                            @else
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-md bg-slate-100 text-slate-500 uppercase tracking-wider">
                                    Mendatang
                                </span>
                            @endif
                        </div>
                        <h4 class="font-bold text-ink-900 text-lg mb-1">{{ $jadwal->kelas->nama ?? '-' }}</h4>
                        <p class="text-sm text-slate-600 font-medium flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            {{ $jadwal->mapel->nama ?? '-' }}
                        </p>

                        @if($jadwal->status_jurnal === 'sudah_diisi')
                            <div class="mt-4 flex items-center justify-center gap-1.5 text-[11px] font-bold text-slate-400 bg-slate-50 py-2 rounded-lg border border-slate-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Jurnal Pelajaran #JRN-{{ \Carbon\Carbon::parse($jadwal->jurnal_ref->tanggal)->format('Ymd') }}-{{ str_pad($jadwal->jurnal_ref->id, 2, '0', STR_PAD_LEFT) }}
                            </div>
                        @elseif($jadwal->status_jurnal === 'belum_diisi')
                            <a href="{{ route('guru.jurnal.form', implode(',', $jadwal->jadwal_ids)) }}" class="mt-4 w-full bg-[#121A2F] hover:bg-[#1f2937] text-white text-sm font-semibold py-3 rounded-xl flex items-center justify-center gap-2 transition-colors active:scale-[0.98]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Isi Jurnal Sekarang
                            </a>
                        @else
                            <div class="mt-4 w-full bg-slate-50 border border-slate-200 text-slate-400 text-center py-2.5 rounded-xl text-xs font-bold cursor-not-allowed">
                                Belum Dapat Diisi
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-slate-50/50 rounded-2xl p-8 border border-slate-100 border-dashed text-center">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        </div>
                        <h4 class="text-slate-800 font-bold mb-1">Hari yang Kosong</h4>
                        <p class="text-sm text-slate-500 font-medium">Anda tidak memiliki jadwal mengajar hari ini.</p>
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
