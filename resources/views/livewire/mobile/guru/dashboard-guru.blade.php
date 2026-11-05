@php
    $sekarang = now();
    $jam = (int) $sekarang->format('H');
    $sapaan = match (true) { $jam < 11 => 'Selamat pagi', $jam < 15 => 'Selamat siang', $jam < 18 => 'Selamat sore', default => 'Selamat malam' };
    $namaDepan = \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first();
    $unreadCount = auth()->user()->unreadNotifications->count();
    $jamSekarang = $sekarang->format('H:i');

    // Kelas yang sedang / berikutnya berlangsung
    $sorotan = collect($jadwalHariIni)->first(fn ($j) => $j['jam_selesai'] > $jamSekarang);
    $sedangBerlangsung = $sorotan && $sorotan['jam_mulai'] <= $jamSekarang;
    $belumIsi = collect($jadwalHariIni)->where('status', '!=', 'diisi')->count();

    $aksiCepat = [
        ['Jadwal', route('guru.jadwal'), 'jadwal', false],
        ['Isi Jurnal', route('guru.jurnal.riwayat'), 'jurnal', $belumIsi > 0],
        ['Riwayat Absen', route('akademik.absensi.guru.riwayat'), 'riwayat', false],
        ['Notifikasi', route('guru.notifikasi'), 'lonceng', $unreadCount > 0],
        ['Kalender', null, 'kegiatan', false],
    ];
@endphp

<div class="pb-6 font-body" x-data="{ openCalendar: false }">

    {{-- HERO --}}
    <section class="relative overflow-hidden rounded-b-[2rem] bg-console px-5 pt-[max(env(safe-area-inset-top),1rem)] pb-20 text-white">
        <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="" aria-hidden="true"
             class="pointer-events-none absolute -right-10 -bottom-12 h-56 w-56 object-contain opacity-[0.07]">

        <div class="relative flex h-14 items-center justify-between">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="" class="h-8 w-8 object-contain">
                <span class="font-heading text-[15px] font-semibold tracking-tight">SIATU <span class="text-white/50">·</span> <span class="text-gold-300">Guru</span></span>
            </div>
            @include('components.layouts.partials.pwa-lonceng', ['unreadCount' => $unreadCount, 'isGuru' => true])
        </div>

        <div class="relative mt-4">
            <p class="text-sm text-white/65">{{ $sapaan }},</p>
            <h1 class="mt-0.5 font-heading text-[1.7rem] font-semibold leading-tight">{{ $namaDepan }}</h1>
            <p class="mt-1.5 text-[13px] text-white/55">{{ $sekarang->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
    </section>

    {{-- KARTU ABSEN (menumpuk di tepi hero) --}}
    <section class="relative z-10 -mt-14 px-4">
        <div class="rounded-3xl bg-white p-4 shadow-float ring-1 ring-ivory-300">
            <div class="grid grid-cols-2 divide-x divide-ivory-300">
                @foreach ([['Masuk', $absenMasuk], ['Pulang', $absenPulang]] as [$label, $absen])
                    <div class="{{ $loop->first ? 'pr-4' : 'pl-4' }}">
                        <p class="text-[12px] font-medium text-navy-400">Absen {{ strtolower($label) }}</p>
                        @if ($absen)
                            <p class="mt-1 font-heading text-2xl font-semibold tabular-nums text-navy-900">{{ \Carbon\Carbon::parse($absen->waktu_absen)->format('H:i') }}</p>
                            <span @class([
                                'mt-1 inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold',
                                'bg-ok-50 text-ok-500' => in_array($absen->status, ['hadir']),
                                'bg-warn-50 text-warn-500' => in_array($absen->status, ['terlambat', 'pulang_cepat']),
                                'bg-ivory-200 text-navy-600' => ! in_array($absen->status, ['hadir', 'terlambat', 'pulang_cepat']),
                            ])>
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ ucwords(str_replace('_', ' ', $absen->status)) }}
                            </span>
                        @else
                            <p class="mt-1 font-heading text-2xl font-semibold text-navy-200">--:--</p>
                            <span class="mt-1 inline-block text-[11px] text-navy-400">Belum tercatat</span>
                        @endif
                    </div>
                @endforeach
            </div>

            @if (! ($absenMasuk && $absenPulang))
                <a wire:navigate href="{{ route('akademik.absensi.guru.form') }}"
                   class="mt-4 flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-navy-900 text-[15px] font-semibold text-white transition hover:bg-navy-800 active:scale-[0.98]">
                    <x-ui.icon name="absen" class="h-5 w-5 text-gold-300" />
                    {{ $absenMasuk ? 'Absen pulang' : 'Absen masuk sekarang' }}
                </a>
            @else
                <p class="mt-4 flex items-center justify-center gap-2 rounded-2xl bg-ok-50 py-3 text-[14px] font-semibold text-ok-500">
                    <x-ui.icon name="cek" class="h-4 w-4" /> Absen hari ini lengkap
                </p>
            @endif
        </div>
    </section>

    {{-- AKSES CEPAT (geser horizontal) --}}
    <section class="mt-6" aria-label="Akses cepat">
        <div class="flex snap-x gap-3 overflow-x-auto px-4 pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @foreach ($aksiCepat as [$label, $href, $ikon, $titik])
                <{{ $href ? 'a' : 'button' }} @if ($href) wire:navigate href="{{ $href }}" @else type="button" @click="openCalendar = true" @endif
                    class="flex w-[4.75rem] shrink-0 snap-start flex-col items-center gap-2 text-center">
                    <span class="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-navy-800 shadow-sm ring-1 ring-ivory-300 transition active:scale-95">
                        <x-ui.icon :name="$ikon" class="h-6 w-6" />
                        @if ($titik)<span class="absolute right-2 top-2 h-2.5 w-2.5 rounded-full bg-gold-500 ring-2 ring-white"></span>@endif
                    </span>
                    <span class="text-[12px] font-medium leading-tight text-navy-700">{{ $label }}</span>
                </{{ $href ? 'a' : 'button' }}>
            @endforeach
        </div>
    </section>

    {{-- SOROTAN KELAS --}}
    @if ($sorotan)
        <section class="mt-6 px-4">
            <div class="relative overflow-hidden rounded-3xl bg-navy-800 p-5 text-white">
                <span class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-gold-400/15"></span>
                <p class="relative inline-flex items-center gap-1.5 rounded-full bg-white/10 px-2.5 py-1 text-[11px] font-semibold">
                    <span class="h-1.5 w-1.5 rounded-full {{ $sedangBerlangsung ? 'bg-emerald-400 animate-pulse' : 'bg-gold-300' }}"></span>
                    {{ $sedangBerlangsung ? 'Sedang berlangsung' : 'Kelas berikutnya' }}
                </p>
                <h2 class="relative mt-3 font-heading text-xl font-semibold leading-snug">{{ $sorotan['mapel'] }}</h2>
                <p class="relative mt-0.5 text-sm text-white/70">{{ $sorotan['kelas'] }} · {{ $sorotan['jam_mulai'] }}–{{ $sorotan['jam_selesai'] }}</p>
                @if ($sorotan['status'] === 'belum_diisi')
                    <a wire:navigate href="{{ route('guru.jurnal.form', implode(',', $sorotan['sesi_ids'])) }}"
                       class="relative mt-4 inline-flex h-11 items-center gap-2 rounded-2xl bg-gold-400 px-4 text-sm font-semibold text-navy-950 transition hover:bg-gold-300 active:scale-[0.98]">
                        <x-ui.icon name="jurnal" class="h-4 w-4" /> Isi jurnal
                    </a>
                @endif
            </div>
        </section>
    @endif

    {{-- RINGKASAN --}}
    <section class="mt-6 grid grid-cols-2 gap-3 px-4">
        <div class="rounded-2xl bg-white p-4 ring-1 ring-ivory-300">
            <p class="text-[12px] font-medium text-navy-400">Kehadiran</p>
            <p class="mt-1 font-heading text-2xl font-semibold text-navy-900">{{ $persentaseKehadiran }}<span class="text-base text-navy-300">%</span></p>
            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-ivory-300"><div class="h-full rounded-full bg-ok-500" style="width: {{ $persentaseKehadiran }}%"></div></div>
        </div>
        <div class="rounded-2xl bg-white p-4 ring-1 ring-ivory-300">
            <p class="text-[12px] font-medium text-navy-400">Jurnal terisi</p>
            <p class="mt-1 font-heading text-2xl font-semibold text-navy-900">{{ $totalJurnalTerisi }} <span class="text-sm font-medium text-navy-300">sesi</span></p>
            <p class="mt-1.5 text-[12px] text-navy-400">Semester ini</p>
        </div>
    </section>

    {{-- MENGAJAR HARI INI --}}
    <section class="mt-7 px-4">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-heading text-[17px] font-semibold text-navy-900">Mengajar hari ini</h2>
            <a wire:navigate href="{{ route('guru.jadwal') }}" class="text-[13px] font-semibold text-navy-600 hover:text-navy-900">Lihat semua</a>
        </div>

        @forelse ($jadwalHariIni as $j)
            @php $lewat = $j['jam_selesai'] <= $jamSekarang; @endphp
            <article class="mb-3 flex gap-3.5 rounded-2xl bg-white p-4 ring-1 {{ $j['status'] === 'belum_diisi' ? 'ring-gold-300/70' : 'ring-ivory-300' }}">
                <div class="w-14 shrink-0 text-center">
                    <p class="font-heading text-[15px] font-semibold tabular-nums text-navy-900">{{ $j['jam_mulai'] }}</p>
                    <p class="text-[12px] tabular-nums text-navy-400">{{ $j['jam_selesai'] }}</p>
                </div>
                <div class="min-w-0 flex-1 border-l border-ivory-300 pl-3.5">
                    <h3 class="truncate text-[15px] font-semibold text-navy-900">{{ $j['mapel'] }}</h3>
                    <p class="text-[13px] text-navy-500">{{ $j['kelas'] }}</p>
                    <div class="mt-2.5 flex items-center justify-between gap-2">
                        @if ($j['status'] === 'diisi')
                            <span class="inline-flex items-center gap-1 rounded-full bg-ok-50 px-2 py-0.5 text-[11px] font-semibold text-ok-500"><x-ui.icon name="cek" class="h-3 w-3" /> Jurnal terisi</span>
                        @elseif ($j['status'] === 'tidak_wajib')
                            <span class="rounded-full bg-ivory-200 px-2 py-0.5 text-[11px] font-semibold text-navy-500">{{ $j['catatan'] ?? 'Tergeser kegiatan' }}</span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-warn-50 px-2 py-0.5 text-[11px] font-semibold text-warn-500">{{ $lewat ? 'Jurnal belum diisi' : 'Belum dimulai' }}</span>
                            <a wire:navigate href="{{ route('guru.jurnal.form', implode(',', $j['sesi_ids'])) }}" class="inline-flex h-9 items-center gap-1 rounded-full bg-navy-900 px-3.5 text-[13px] font-semibold text-white active:scale-95">Isi jurnal</a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-ivory-400 bg-ivory-50 px-6 py-10 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-navy-300 ring-1 ring-ivory-300"><x-ui.icon name="jadwal" /></span>
                <h3 class="mt-3 text-[15px] font-semibold text-navy-800">Tidak ada jadwal mengajar</h3>
                <p class="mt-1 text-[13px] text-navy-400">Anda tidak memiliki kelas hari ini.</p>
            </div>
        @endforelse
    </section>

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

