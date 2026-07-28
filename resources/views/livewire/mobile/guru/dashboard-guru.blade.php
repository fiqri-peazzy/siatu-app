<div class="space-y-6 animate-fade-in pb-10">
    <!-- Header Greeting -->
    <div class="mt-4">
        <h2 class="text-2xl font-bold text-slate-800">Halo, {{ Auth::user()->name }}</h2>
        <p class="text-slate-500 text-sm mt-1">Selamat datang di portal Guru SMAMGO.</p>
    </div>

    <!-- Status Absensi -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex items-center justify-between relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 rounded-full blur-2xl group-hover:bg-indigo-100 transition-colors"></div>
        <div class="relative z-10">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Status Kehadiran</p>
            @if($absensiHariIni)
                <div class="flex items-center gap-2">
                    <span class="flex w-3 h-3 bg-emerald-500 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                    <span class="font-bold text-slate-800 text-lg">Sudah Absen Masuk</span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Jam masuk: {{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}</p>
            @else
                <div class="flex items-center gap-2">
                    <span class="flex w-3 h-3 bg-rose-500 rounded-full shadow-[0_0_10px_rgba(244,63,94,0.5)]"></span>
                    <span class="font-bold text-slate-800 text-lg">Belum Absen</span>
                </div>
                <p class="text-xs text-rose-400 mt-1">Silakan lakukan absensi hari ini.</p>
            @endif
        </div>
        <div class="relative z-10">
            <a href="{{ route('akademik.absensi.guru.form') }}" class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm active:scale-95">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>

    <!-- Ringkasan Jurnal -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-3xl p-5 text-white shadow-lg shadow-indigo-200 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2zm0 3.83L18.17 20H5.83L12 5.83z"></path></svg>
            </div>
            <p class="text-indigo-100 text-xs font-semibold mb-1">Total Jam Mengajar</p>
            <h3 class="text-4xl font-bold">{{ $totalJam }}</h3>
            <p class="text-xs text-indigo-100 mt-1">Hari ini</p>
        </div>
        
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 relative overflow-hidden group">
            <p class="text-slate-500 text-xs font-semibold mb-1">Jurnal Terisi</p>
            <div class="flex items-end gap-2">
                <h3 class="text-4xl font-bold text-slate-800">{{ $totalTerisi }}</h3>
                <span class="text-sm font-semibold text-slate-400 mb-1.5">/ {{ $totalJam }}</span>
            </div>
            
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3 overflow-hidden">
                @php
                    $percentage = $totalJam > 0 ? ($totalTerisi / $totalJam) * 100 : 0;
                @endphp
                <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $percentage }}%"></div>
            </div>
        </div>
    </div>

    <!-- Jadwal Mengajar Hari Ini -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">Jadwal Hari Ini</h3>
            <span class="text-xs font-semibold px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMM') }}</span>
        </div>

        @if(count($jadwalHariIni) > 0)
            <div class="space-y-4">
                @foreach($jadwalHariIni as $j)
                    <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-100 transition-all hover:shadow-md hover:border-indigo-100 relative overflow-hidden">
                        @if($j['status'] === 'diisi')
                            <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>
                        @else
                            <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-400"></div>
                        @endif
                        
                        <div class="pl-2 flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg {{ $j['status'] === 'diisi' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} uppercase tracking-wider">
                                        Jam ke-{{ $j['jam_ke'] }}
                                    </span>
                                    <span class="text-xs font-semibold text-slate-400">
                                        {{ $j['jam_mulai'] }} - {{ $j['jam_selesai'] }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-slate-800 text-lg leading-tight">{{ $j['mapel'] }}</h4>
                                <p class="text-sm text-slate-500 font-medium mt-0.5">Kelas {{ $j['kelas'] }}</p>
                            </div>
                            
                            <div>
                                @if($j['status'] === 'diisi')
                                    <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @else
                                    <a href="{{ route('guru.jurnal.form', $j['id']) }}" class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all active:scale-95">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-3xl p-8 border border-slate-100 flex flex-col items-center justify-center text-center shadow-sm">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path></svg>
                </div>
                <h4 class="font-bold text-slate-700">Tidak Ada Jadwal</h4>
                <p class="text-sm text-slate-500 mt-1">Anda tidak memiliki jadwal mengajar hari ini.</p>
            </div>
        @endif
    </div>
</div>
