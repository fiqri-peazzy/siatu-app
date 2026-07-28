<div class="space-y-6 animate-fade-in pb-10">
    <!-- Header Greeting -->
    <div class="mt-4">
        <h2 class="text-2xl font-extrabold text-ink-900 tracking-tight">Halo, {{ Auth::user()->name }}</h2>
        <p class="text-slate-500 text-sm mt-1 font-medium">Selamat datang di portal Guru SMAMGO.</p>
    </div>

    <!-- Status Absensi -->
    <div class="bg-navy-deep rounded-[1.5rem] p-6 shadow-[0_8px_30px_rgba(18,42,82,0.15)] flex items-center justify-between relative overflow-hidden">
        <!-- Inner subtle highlight for double-bezel feel -->
        <div class="absolute inset-0 border border-white/10 rounded-[1.5rem] pointer-events-none"></div>
        
        <div class="relative z-10">
            <p class="text-[10px] font-bold text-sky-mist uppercase tracking-[0.1em] mb-1.5 opacity-80">Status Kehadiran</p>
            @if($absensiHariIni)
                <div class="flex items-center gap-2.5 mb-1">
                    <span class="flex w-2.5 h-2.5 bg-success-teal rounded-full shadow-[0_0_8px_rgba(18,134,111,0.6)]"></span>
                    <span class="font-bold text-white text-lg">Sudah Absen Masuk</span>
                </div>
                <p class="text-xs text-sky-mist opacity-80">Jam masuk: <span class="font-mono text-[11px] ml-0.5">{{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}</span></p>
            @else
                <div class="flex items-center gap-2.5 mb-1">
                    <span class="flex w-2.5 h-2.5 bg-warning-amber rounded-full shadow-[0_0_8px_rgba(201,138,27,0.6)]"></span>
                    <span class="font-bold text-white text-lg">Belum Absen</span>
                </div>
                <p class="text-xs text-sky-mist opacity-80">Silakan lakukan absensi hari ini.</p>
            @endif
        </div>
        <div class="relative z-10 flex-shrink-0">
            <a href="{{ route('akademik.absensi.guru.form') }}" class="w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white hover:bg-white/20 transition-colors active:scale-95">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>

    <!-- Ringkasan Jurnal -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-[1.25rem] p-5 shadow-sm border border-line-200">
            <p class="text-slate-500 text-[11px] font-semibold uppercase tracking-wider mb-1">Total Mengajar</p>
            <div class="flex items-baseline gap-1 mt-2">
                <h3 class="text-3xl font-extrabold text-ink-900">{{ $totalJam }}</h3>
                <span class="text-xs font-semibold text-slate-400">jam</span>
            </div>
        </div>

        <div class="bg-white rounded-[1.25rem] p-5 shadow-sm border border-line-200 flex flex-col justify-between">
            <p class="text-slate-500 text-[11px] font-semibold uppercase tracking-wider mb-1">Jurnal Terisi</p>
            <div class="flex items-baseline gap-1 mt-2">
                <h3 class="text-3xl font-extrabold text-ink-900">{{ $totalTerisi }}</h3>
                <span class="text-sm font-semibold text-slate-400">/ {{ $totalJam }}</span>
            </div>
            
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3 overflow-hidden">
                @php
                    $percentage = $totalJam > 0 ? ($totalTerisi / $totalJam) * 100 : 0;
                @endphp
                <div class="bg-signal-blue h-1.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $percentage }}%"></div>
            </div>
        </div>
    </div>

    <!-- Jadwal Mengajar Hari Ini -->
    <div class="mt-8">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-ink-900">Jadwal Mengajar</h3>
            <span class="text-[11px] font-bold px-3 py-1 bg-white border border-line-200 text-slate-500 rounded-full shadow-sm">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMM') }}</span>
        </div>

        @if(count($jadwalHariIni) > 0)
            <div class="relative pl-3 space-y-6 before:absolute before:inset-0 before:ml-[1.35rem] before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-line-200">
                @foreach($jadwalHariIni as $j)
                    <div class="relative flex items-start gap-4">
                        <!-- Dot Timeline -->
                        <div class="absolute left-0 w-5 h-5 flex items-center justify-center bg-paper">
                            @if($j['status'] === 'diisi')
                                <div class="w-3.5 h-3.5 bg-signal-blue rounded-full ring-4 ring-paper shadow-sm z-10"></div>
                            @else
                                <div class="w-3.5 h-3.5 bg-paper border-2 border-warning-amber rounded-full ring-4 ring-paper shadow-sm z-10"></div>
                            @endif
                        </div>

                        <!-- Card Content -->
                        <div class="flex-1 ml-6">
                            <div class="bg-white rounded-2xl p-4 shadow-sm border border-line-200 transition-all hover:shadow-md">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded border uppercase tracking-wider {{ $j['status'] === 'diisi' ? 'bg-signal-blue/5 border-signal-blue/20 text-signal-blue' : 'bg-warning-amber/5 border-warning-amber/20 text-warning-amber' }}">
                                        Jam ke-{{ $j['jam_ke'] }}
                                    </span>
                                    <span class="font-mono text-[11px] font-medium text-slate-500">
                                        {{ $j['jam_mulai'] }} - {{ $j['jam_selesai'] }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-ink-900 text-[15px] leading-tight mb-1">{{ $j['mapel'] }}</h4>
                                <div class="flex items-center justify-between mt-3">
                                    <p class="text-xs text-slate-500 font-medium flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        Kelas {{ $j['kelas'] }}
                                    </p>
                                    
                                    @if($j['status'] === 'diisi')
                                        <div class="text-[11px] font-bold text-success-teal flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                            Selesai
                                        </div>
                                    @else
                                        <a href="{{ route('guru.jurnal.form', $j['id']) }}" class="flex items-center gap-1 text-[11px] font-bold text-signal-blue bg-signal-blue/5 hover:bg-signal-blue/10 px-2.5 py-1 rounded-md transition-colors">
                                            Isi Jurnal
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl p-8 border border-line-200 flex flex-col items-center justify-center text-center shadow-sm">
                <div class="w-16 h-16 bg-paper rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path></svg>
                </div>
                <h4 class="font-bold text-ink-900">Tidak Ada Jadwal</h4>
                <p class="text-sm text-slate-500 mt-1 font-medium">Anda tidak memiliki jadwal mengajar hari ini.</p>
            </div>
        @endif
    </div>
</div>
