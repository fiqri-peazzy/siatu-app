<?php
$bladeContent = <<<HTML
<x-layouts.app>
    <x-slot name="header">
        Dashboard
    </x-slot>

    @php
        use Carbon\Carbon;
        use App\Models\SuratMasuk;
        use App\Models\SuratKeluar;
        use App\Models\Master\MasterGuru;
        use App\Models\AbsensiGuru;

        // 1. Data Surat 6 Bulan Terakhir
        \$chartLabelsSurat = [];
        \$chartDataMasuk = [];
        \$chartDataKeluar = [];
        
        \$startOfMonth = Carbon::now()->startOfMonth();
        for (\$i = 5; \$i >= 0; \$i--) {
            \$date = \$startOfMonth->copy()->subMonths(\$i);
            \$chartLabelsSurat[] = \$date->translatedFormat('M Y');
            
            \$chartDataMasuk[] = SuratMasuk::whereYear('created_at', \$date->year)
                ->whereMonth('created_at', \$date->month)
                ->count();
                
            \$chartDataKeluar[] = SuratKeluar::whereYear('created_at', \$date->year)
                ->whereMonth('created_at', \$date->month)
                ->count();
        }

        // 2. Data Absensi Guru Hari Ini
        \$totalGuru = MasterGuru::count();
        \$today = Carbon::today()->format('Y-m-d');
        
        \$guruHadir = AbsensiGuru::where('tanggal', \$today)
            ->where('jenis', 'masuk')
            ->where('status', 'hadir')
            ->count();
            
        \$guruIzin = AbsensiGuru::where('tanggal', \$today)
            ->where('jenis', 'masuk')
            ->where('status', 'izin')
            ->count();
            
        \$guruSakit = AbsensiGuru::where('tanggal', \$today)
            ->where('jenis', 'masuk')
            ->where('status', 'sakit')
            ->count();
            
        \$guruBelumAbsen = \$totalGuru - (\$guruHadir + \$guruIzin + \$guruSakit);
        if (\$guruBelumAbsen < 0) \$guruBelumAbsen = 0;
        
        \$chartDataAbsensi = [\$guruHadir, \$guruIzin, \$guruSakit, \$guruBelumAbsen];
        
        // Data Surat Bulan Ini untuk Summary Cards
        \$countSuratMasukBulanIni = SuratMasuk::whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)->count();
        \$countSuratKeluarBulanIni = SuratKeluar::whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)->count();
    @endphp

    <div class="space-y-8">

        {{-- Greeting --}}
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-on-surface" style="letter-spacing: -0.02em;">
                Selamat datang, {{ auth()->user()->name }}
            </h2>
            <p class="text-sm text-on-surface-variant mt-1">
                {{ now()->translatedFormat('l, d F Y') }} · Tahun Pelajaran 2024/2025
            </p>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-surface-container-lowest rounded-xl p-6 tonal-shadow">
                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-widest">Surat Masuk</p>
                <p class="text-4xl font-bold text-on-surface mt-2" style="letter-spacing: -0.02em;">{{ \$countSuratMasukBulanIni }}</p>
                <p class="text-xs text-on-surface-variant mt-1">Bulan ini</p>
            </div>

            <div class="bg-surface-container-lowest rounded-xl p-6 tonal-shadow">
                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-widest">Surat Keluar</p>
                <p class="text-4xl font-bold text-on-surface mt-2" style="letter-spacing: -0.02em;">{{ \$countSuratKeluarBulanIni }}</p>
                <p class="text-xs text-on-surface-variant mt-1">Bulan ini</p>
            </div>

            <div class="bg-surface-container-lowest rounded-xl p-6 tonal-shadow">
                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-widest">Guru & Staf</p>
                <p class="text-4xl font-bold text-on-surface mt-2" style="letter-spacing: -0.02em;">{{ \$totalGuru }}</p>
                <p class="text-xs text-on-surface-variant mt-1">Aktif terdaftar</p>
            </div>

            <div class="rounded-xl p-6 signature-gradient tonal-shadow">
                <p class="text-xs font-semibold text-white/70 uppercase tracking-widest">Pengguna Sistem</p>
                <p class="text-4xl font-bold text-white mt-2" style="letter-spacing: -0.02em;">{{ \App\Models\User::count() }}</p>
                <p class="text-xs text-white/70 mt-1">Semua role</p>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Surat Chart (2/3 width on large screens) --}}
            <div class="bg-surface-container-lowest rounded-xl p-6 tonal-shadow lg:col-span-2">
                <h3 class="text-lg font-semibold text-on-surface mb-4">Statistik Surat (6 Bulan Terakhir)</h3>
                <div id="chart-surat" class="w-full h-80"></div>
            </div>

            {{-- Absensi Chart (1/3 width on large screens) --}}
            <div class="bg-surface-container-lowest rounded-xl p-6 tonal-shadow">
                <h3 class="text-lg font-semibold text-on-surface mb-4">Absensi Guru Hari Ini</h3>
                <div id="chart-absensi" class="w-full h-80 flex items-center justify-center"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Role Info --}}
            <div class="bg-surface-container-lowest rounded-xl p-6 tonal-shadow">
                <h3 class="text-sm font-semibold text-on-surface-variant uppercase tracking-widest mb-4">Informasi Akun</h3>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full signature-gradient flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-semibold text-on-surface">{{ auth()->user()->name }}</p>
                        <p class="text-sm text-on-surface-variant">{{ auth()->user()->email }}</p>
                        <span class="inline-block mt-1 px-3 py-0.5 bg-secondary-container text-secondary text-xs font-semibold rounded-full">
                            {{ auth()->user()->role_label }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Widget: Pending Approval & Disposisi --}}
            <div class="flex flex-col gap-4">
                @if(auth()->user()->hasRole('kepala-sekolah'))
                    @php
                        \$pendingApprovals = \App\Models\SuratKeluar::where('status', \App\Enums\StatusSuratKeluar::MENUNGGU_APPROVAL)->count();
                    @endphp
                    @if(\$pendingApprovals > 0)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-xl shadow-sm h-full flex flex-col justify-center">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Anda memiliki <strong>{{ \$pendingApprovals }}</strong> draf surat keluar yang menunggu persetujuan (approval).
                                    <br>
                                    <a href="{{ route('administrasi.surat-keluar.index') }}" class="inline-block mt-2 font-medium underline text-yellow-700 hover:text-yellow-600">Tinjau sekarang &rarr;</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                @endif

                @php
                    \$pendingDisposisi = \App\Models\Disposisi::with('suratMasuk')
                        ->where('ke_user_id', auth()->id())
                        ->where('status', \App\Enums\StatusDisposisi::DITERIMA)
                        ->get();
                @endphp
                @if(\$pendingDisposisi->count() > 0)
                <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-xl shadow-sm h-full flex flex-col justify-center">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-indigo-800">Disposisi Menunggu Tindak Lanjut ({{ \$pendingDisposisi->count() }})</h3>
                            <div class="mt-2 text-sm text-indigo-700 max-h-24 overflow-y-auto">
                                <ul role="list" class="list-disc space-y-1 pl-5">
                                    @foreach(\$pendingDisposisi as \$disp)
                                    <li>
                                        <a href="{{ route('administrasi.surat-masuk.show', \$disp->surat_masuk_id) }}" class="hover:underline font-semibold">
                                            {{ \$disp->suratMasuk->no_agenda ?? 'Surat' }}
                                        </a> 
                                        - {{ \Illuminate\Support\Str::limit(\$disp->suratMasuk->perihal ?? '', 40) }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Tombol Navigasi Portal Absensi Mobile (Double Portal) --}}
        @if(auth()->user()->hasRole(['guru', 'staf-tu', 'admin-tu', 'kepala-sekolah', 'super-admin']))
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 shadow-lg relative overflow-hidden mt-6">
                <!-- Background decoration -->
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white opacity-10 rounded-full blur-xl"></div>
                <div class="absolute bottom-0 right-10 w-24 h-24 bg-white opacity-10 rounded-full blur-lg"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-1 tracking-tight">Portal Absensi Anda</h3>
                        <p class="text-indigo-100 text-sm">Rekam presensi kehadiran harian (wajah & lokasi) Anda langsung melalui portal khusus ini.</p>
                    </div>
                    @php
                        \$absenRoute = auth()->user()->hasRole('guru') ? route('akademik.absensi.guru.form') : route('akademik.absensi.tendik.form');
                    @endphp
                    <a href="{{ \$absenRoute }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-indigo-600 font-bold rounded-lg shadow-sm hover:bg-indigo-50 transition-colors shrink-0 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Mulai Absen Sekarang
                    </a>
                </div>
            </div>
        @endif

    </div>

    {{-- ApexCharts Library & Init Scripts --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data dari PHP
            const labelsSurat = @json(\$chartLabelsSurat);
            const dataMasuk = @json(\$chartDataMasuk);
            const dataKeluar = @json(\$chartDataKeluar);
            const dataAbsensi = @json(\$chartDataAbsensi);

            // Chart Surat Masuk/Keluar (Bar Chart)
            const optionsSurat = {
                series: [{
                    name: 'Surat Masuk',
                    data: dataMasuk,
                    color: '#6366f1' // indigo-500
                }, {
                    name: 'Surat Keluar',
                    data: dataKeluar,
                    color: '#eab308' // yellow-500
                }],
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '45%',
                        borderRadius: 4
                    },
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: {
                    categories: labelsSurat,
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    title: { text: 'Jumlah Surat' },
                    tickAmount: 5,
                    min: 0
                },
                fill: { opacity: 1 },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " surat"
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                    yaxis: { lines: { show: true } }
                }
            };
            new ApexCharts(document.querySelector("#chart-surat"), optionsSurat).render();

            // Chart Absensi Guru (Donut Chart)
            const optionsAbsensi = {
                series: dataAbsensi,
                labels: ['Hadir', 'Izin', 'Sakit', 'Belum Absen'],
                colors: ['#22c55e', '#3b82f6', '#f59e0b', '#cbd5e1'],
                chart: {
                    type: 'donut',
                    height: 320,
                    fontFamily: 'Inter, sans-serif'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                name: { show: true },
                                value: { show: true, fontSize: '24px', fontWeight: 'bold' },
                                total: {
                                    show: true,
                                    showAlways: true,
                                    label: 'Total Guru',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: { enabled: false },
                stroke: { width: 2, colors: ['#ffffff'] },
                legend: {
                    position: 'bottom',
                    offsetY: 0,
                    markers: { radius: 12 }
                }
            };
            new ApexCharts(document.querySelector("#chart-absensi"), optionsAbsensi).render();
        });
    </script>
    @endpush
</x-layouts.app>
HTML;

file_put_contents('resources/views/dashboard.blade.php', $bladeContent);
echo "View generated successfully.\n";
