<div class="space-y-6 animate-fade-in pb-10" x-data="{ openCalendar: false }">
    <!-- Status Absensi & Top Card -->
    <div class="bg-[#121A2F] rounded-xl p-5 shadow-sm mt-4 flex flex-col gap-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white/5 blur-2xl"></div>
        <div class="flex justify-between items-start relative z-10">
            <div>
                <p class="text-slate-400 text-sm">Selamat Pagi,</p>
                <h2 class="text-lg font-bold mt-0.5">{{ explode(' ', Auth::user()->name)[0] }}</h2>
            </div>
            <div class="text-right">
                <h1 class="text-3xl font-extrabold font-mono tracking-tight">{{ \Carbon\Carbon::now()->format('H:i') }}</h1>
                <p class="text-slate-400 text-xs mt-1">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMM') }}</p>
            </div>
        </div>

        <div class="flex gap-3 mt-2 relative z-10">
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
            <button class="w-12 h-12 flex-shrink-0 bg-white/10 border border-white/20 hover:bg-white/20 active:scale-95 transition-all rounded-xl flex items-center justify-center backdrop-blur-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h1.5v-1.5H18v1.5h1.5v-1.5h-1.5v-1.5h-1.5v1.5h-1.5v1.5h-1.5v-1.5H12v1.5h1.5v1.5z" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-6 h-6 rounded-md bg-blue-50 flex items-center justify-center text-blue-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wide">Kehadiran</p>
            </div>
            <div class="flex items-end gap-1.5">
                <h3 class="text-2xl font-bold text-ink-900">{{ $persentaseKehadiran }}<span class="text-lg text-slate-400 font-medium">%</span></h3>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-6 h-6 rounded-md bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wide">Jurnal Terisi</p>
            </div>
            <div class="flex items-baseline gap-1">
                <h3 class="text-2xl font-bold text-ink-900">{{ $totalJurnalTerisi }} <span class="text-sm font-medium text-slate-400">Sesi</span></h3>
            </div>
        </div>
    </div>

    <!-- Jadwal Mengajar -->
    <div class="mt-8">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold text-ink-900">Jadwal Hari Ini</h3>
            <button @click="openCalendar = true" class="text-sm font-semibold text-[#3B65F3] bg-blue-50 px-3 py-1.5 rounded-lg active:scale-95 transition-transform flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Kalender
            </button>
        </div>

        @if(count($jadwalHariIni) > 0)
            <div class="space-y-4">
                @foreach($jadwalHariIni as $j)
                    <div class="bg-white rounded-[16px] p-4 shadow-sm ring-1 {{ $j['status'] === 'diisi' ? 'ring-slate-100' : 'ring-[#FCD34D] shadow-[#FEF3C7]/50' }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full {{ $j['status'] === 'diisi' ? 'bg-[#059669]' : 'bg-[#D97706]' }}"></div>
                                <span class="font-mono text-sm font-bold text-slate-700 tracking-tight">
                                    {{ $j['jam_mulai'] }} - {{ $j['jam_selesai'] }}
                                </span>
                            </div>
                            @if($j['status'] === 'diisi')
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-md bg-[#ECFDF5] text-[#059669] uppercase tracking-wider">
                                    Selesai
                                </span>
                            @else
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-md bg-[#FFFBEB] text-[#D97706] uppercase tracking-wider">
                                    Belum Diisi
                                </span>
                            @endif
                        </div>
                        <h4 class="font-bold text-ink-900 text-lg mb-1">{{ $j['kelas'] }}</h4>
                        <p class="text-sm text-slate-600 font-medium flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            {{ $j['mapel'] }}
                        </p>
                        
                        @if($j['status'] !== 'diisi')
                            <a href="{{ route('guru.jurnal.form', implode(',', $j['jadwal_ids'])) }}" class="mt-4 w-full bg-[#121A2F] hover:bg-[#1f2937] text-white text-sm font-semibold py-3 rounded-xl flex items-center justify-center gap-2 transition-colors active:scale-[0.98]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Isi Jurnal Sekarang
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-slate-50/50 rounded-2xl p-8 border border-slate-100 border-dashed text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                </div>
                <h4 class="text-slate-800 font-bold mb-1">Hari yang Kosong</h4>
                <p class="text-sm text-slate-500 font-medium">Anda tidak memiliki jadwal mengajar hari ini.</p>
            </div>
        @endif
    </div>

    <!-- Calendar Modal (Alpine.js) -->
    <template x-teleport="body">
        <div x-show="openCalendar" style="display: none;" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="openCalendar" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="openCalendar" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     @click.away="openCalendar = false"
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm w-full">
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-slate-900">Kalender Mengajar</h3>
                            <button @click="openCalendar = false" class="text-slate-400 hover:text-slate-500 bg-slate-100 rounded-full p-1.5">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        
                        <!-- Simple Calendar Visual -->
                        <div class="border border-slate-100 rounded-xl overflow-hidden relative">
                            <!-- Loading overlay -->
                            <div wire:loading wire:target="prevMonth, nextMonth" class="absolute inset-0 bg-white/50 backdrop-blur-[2px] z-10 flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>

                            <div class="bg-slate-50 px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                                <button wire:click="prevMonth" type="button" class="p-1 hover:bg-slate-200 rounded active:scale-95 transition-transform"><svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
                                <span class="font-bold text-slate-800 text-sm">{{ \Carbon\Carbon::create($calendarYear, $calendarMonth, 1)->locale('id')->isoFormat('MMMM YYYY') }}</span>
                                <button wire:click="nextMonth" type="button" class="p-1 hover:bg-slate-200 rounded active:scale-95 transition-transform"><svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-7 gap-1 text-center mb-2">
                                    <div class="text-xs font-bold text-slate-400">Sn</div>
                                    <div class="text-xs font-bold text-slate-400">Sl</div>
                                    <div class="text-xs font-bold text-slate-400">Rb</div>
                                    <div class="text-xs font-bold text-slate-400">Km</div>
                                    <div class="text-xs font-bold text-slate-400">Jm</div>
                                    <div class="text-xs font-bold text-slate-400">Sb</div>
                                    <div class="text-xs font-bold text-red-400">Mg</div>
                                </div>
                                @php
                                    $startOfMonth = \Carbon\Carbon::create($calendarYear, $calendarMonth, 1)->startOfMonth();
                                    $endOfMonth = $startOfMonth->copy()->endOfMonth();
                                    $startDayOfWeek = $startOfMonth->dayOfWeekIso; // 1 (Monday) - 7 (Sunday)
                                @endphp
                                <div class="grid grid-cols-7 gap-1 text-center">
                                    @for($i = 1; $i < $startDayOfWeek; $i++)
                                        <div class="p-2"></div>
                                    @endfor
                                    
                                    @for($day = 1; $day <= $endOfMonth->day; $day++)
                                        @php
                                            $date = \Carbon\Carbon::create($calendarYear, $calendarMonth, $day);
                                            $dateStr = $date->format('Y-m-d');
                                            $isToday = $date->isToday();
                                            $isSunday = $date->dayOfWeekIso === 7;
                                            
                                            $isHoliday = array_key_exists($dateStr, $calendarHolidays);
                                            // Check if date has schedule: not a holiday, not weekend (or based on schedule days)
                                            $hasSchedule = !$isHoliday && in_array($date->dayOfWeekIso === 7 ? 0 : $date->dayOfWeekIso, $teacherScheduleDays);
                                            
                                            $isSelected = $selectedDate === $dateStr;
                                        @endphp
                                        <div wire:click="selectDate('{{ $dateStr }}')" 
                                             class="relative p-2 text-sm flex flex-col items-center justify-center cursor-pointer transition-colors
                                             {{ $isSelected ? 'bg-[#121A2F] text-white font-bold rounded-lg shadow-md' : 
                                                ($isToday ? 'bg-blue-50 text-blue-600 font-bold rounded-lg' : 
                                                ($isSunday || $isHoliday ? 'text-red-500 font-medium hover:bg-slate-100 rounded-lg' : 'text-slate-700 font-medium hover:bg-slate-100 rounded-lg')) }}">
                                            <span>{{ $day }}</span>
                                            
                                            <!-- Indicators -->
                                            <div class="flex gap-0.5 mt-0.5 h-1">
                                                @if($isHoliday)
                                                    <div class="w-1 h-1 rounded-full bg-red-500"></div>
                                                @endif
                                                @if($hasSchedule)
                                                    <div class="w-1 h-1 rounded-full bg-blue-500"></div>
                                                @endif
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            
                            <!-- Selected Date Details -->
                            @if($selectedDate)
                                <div class="bg-slate-50 border-t border-slate-100 p-4 animate-fade-in">
                                    <h4 class="text-sm font-bold text-slate-800 mb-2">
                                        {{ \Carbon\Carbon::parse($selectedDate)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                                    </h4>
                                    
                                    @if($selectedDateDetails['is_holiday'])
                                        <div class="flex items-start gap-2 bg-red-50 text-red-700 p-3 rounded-lg text-sm">
                                            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <div>
                                                <p class="font-bold">Libur Nasional</p>
                                                <p class="text-red-600/80">{{ $selectedDateDetails['holiday_name'] }}</p>
                                            </div>
                                        </div>
                                    @elseif($selectedDateDetails['has_schedule'])
                                        <div class="space-y-2">
                                            @foreach($selectedDateDetails['schedules'] as $s)
                                                <div class="bg-white border border-slate-200 p-3 rounded-lg text-sm shadow-sm">
                                                    <div class="flex justify-between items-center mb-1">
                                                        <span class="font-bold text-slate-800">{{ $s['kelas'] }}</span>
                                                        <span class="font-mono text-xs text-slate-500">{{ $s['jam_mulai'] }} - {{ $s['jam_selesai'] }}</span>
                                                    </div>
                                                    <p class="text-slate-600">{{ $s['mapel'] }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <p class="text-sm text-slate-500">Tidak ada jadwal mengajar pada hari ini.</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('guru.jadwal') }}" class="w-full bg-[#121A2F] text-white text-center py-2.5 rounded-lg font-bold text-sm">Lihat Jadwal Lengkap</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

