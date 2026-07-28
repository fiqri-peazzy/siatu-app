<div class="space-y-6 animate-fade-in pb-10">
    <!-- Status Absensi & Top Card -->
    <div class="bg-[#121A2F] rounded-xl p-5 shadow-sm mt-4 flex flex-col gap-4 text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-slate-400 text-sm">Selamat Pagi, {{ explode(' ', Auth::user()->name)[0] }}</p>
                <h2 class="text-lg font-bold mt-0.5">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMM YYYY') }}</h2>
            </div>
            <div class="text-right">
                <h1 class="text-3xl font-extrabold font-mono tracking-tight">{{ \Carbon\Carbon::now()->format('H:i') }}</h1>
                <p class="text-slate-400 text-xs">Jam Absen</p>
            </div>
        </div>

        <div class="flex gap-3 mt-2">
            <a href="{{ ($absenMasuk && $absenPulang) ? '#' : route('akademik.absensi.guru.form') }}" class="flex-1 bg-[#3B65F3] hover:bg-blue-600 active:scale-95 transition-all text-white font-semibold py-3 rounded-xl flex items-center justify-center gap-2 shadow-sm {{ ($absenMasuk && $absenPulang) ? 'opacity-50 cursor-not-allowed' : '' }}">
                @if($absenMasuk && $absenPulang)
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    Selesai
                @elseif($absenMasuk)
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Absen Pulang
                @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                    Absen Masuk
                @endif
            </a>
            <button class="w-12 h-12 flex-shrink-0 bg-white/5 border border-white/10 hover:bg-white/10 active:scale-95 transition-all rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h1.5v-1.5H18v1.5h1.5v-1.5h-1.5v-1.5h-1.5v1.5h-1.5v1.5h-1.5v-1.5H12v1.5h1.5v1.5z" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white rounded-xl p-4 shadow-sm border-none ring-1 ring-slate-100">
            <p class="text-slate-600 text-xs font-medium mb-2">Kehadiran Bln Ini</p>
            <div class="flex items-end gap-1.5">
                <h3 class="text-2xl font-bold text-ink-900">98%</h3>
                <span class="text-[10px] font-bold text-[#0D9488] mb-1">▲ 2%</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border-none ring-1 ring-slate-100">
            <p class="text-slate-600 text-xs font-medium mb-2">Jurnal Terisi</p>
            <div class="flex items-baseline gap-1">
                <h3 class="text-2xl font-bold text-ink-900">{{ $totalTerisi }}/{{ $totalJam }}</h3>
            </div>
        </div>
    </div>

    <!-- Jadwal Mengajar -->
    <div class="mt-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold text-ink-900">Jadwal Mengajar Hari Ini</h3>
            <a href="#" class="text-sm font-semibold text-[#3B65F3]">Lihat Kalender</a>
        </div>

        @if(count($jadwalHariIni) > 0)
            <div class="relative pl-4 space-y-5 before:absolute before:inset-0 before:ml-[1.4rem] before:-translate-x-px before:h-full before:w-[1px] before:bg-slate-200">
                @foreach($jadwalHariIni as $j)
                    <div class="relative flex items-start gap-4">
                        <!-- Timeline Dot -->
                        <div class="absolute left-0 w-6 h-6 flex items-center justify-center bg-[#F5F7FC] -ml-[5px]">
                            @if($j['status'] === 'diisi')
                                <div class="w-5 h-5 rounded-full border-2 border-[#121A2F] flex items-center justify-center bg-white shadow-sm z-10">
                                    <div class="w-2 h-2 bg-[#121A2F] rounded-full"></div>
                                </div>
                            @else
                                <div class="w-5 h-5 rounded-full border-2 border-[#D97706] flex items-center justify-center bg-white shadow-sm z-10">
                                    <div class="w-2 h-2 bg-[#D97706] rounded-full"></div>
                                </div>
                            @endif
                        </div>

                        <!-- Card -->
                        <div class="flex-1 ml-6">
                            <div class="bg-white rounded-[14px] p-4 shadow-sm border {{ $j['status'] === 'diisi' ? 'border-slate-100' : 'border-[#D97706]' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-mono text-xs font-semibold text-slate-600">
                                        {{ $j['jam_mulai'] }} - {{ $j['jam_selesai'] }}
                                    </span>
                                    @if($j['status'] === 'diisi')
                                        <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-[#ECFDF5] text-[#059669] uppercase tracking-wide">
                                            Diisi
                                        </span>
                                    @else
                                        <span class="text-[9px] font-bold px-2 py-0.5 rounded bg-[#FFFBEB] text-[#D97706] uppercase tracking-wide">
                                            Belum Diisi
                                        </span>
                                    @endif
                                </div>
                                <h4 class="font-bold text-ink-900 text-base mb-0.5">{{ $j['kelas'] }}</h4>
                                <p class="text-sm text-slate-600 font-medium">{{ $j['mapel'] }}</p>
                                
                                @if($j['status'] !== 'diisi')
                                    <a href="{{ route('guru.jurnal.form', implode(',', $j['jadwal_ids'])) }}" class="mt-4 w-full bg-[#121A2F] hover:bg-[#1f2937] text-white text-sm font-semibold py-2.5 rounded-lg flex items-center justify-center gap-2 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Isi Jurnal Kelas
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl p-6 border border-slate-100 text-center shadow-sm">
                <p class="text-sm text-slate-500 font-medium">Tidak ada jadwal mengajar hari ini.</p>
            </div>
        @endif
    </div>


