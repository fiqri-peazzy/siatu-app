@php
    $jam = (int) now()->format('H');
    $sapaan = match (true) { $jam < 11 => 'Selamat pagi', $jam < 15 => 'Selamat siang', $jam < 18 => 'Selamat sore', default => 'Selamat malam' };
    $persenSiswa = $siswa['tercatat'] ? (int) round($siswa['hadir'] / $siswa['tercatat'] * 100) : null;

    // Daftar tugas konfigurasi: [selesai?, judul, keterangan, route]
    $cekList = [
        [(bool) $data['tahun_ajaran'], 'Tahun ajaran aktif', $data['tahun_ajaran'] ?? 'Belum ada tahun ajaran aktif', route('master.tahun-ajaran')],
        [$data['lokasi_diatur'], 'Lokasi & radius absensi', $data['lokasi_diatur'] ? 'Titik sekolah sudah diatur' : 'Titik koordinat sekolah belum diatur', route('sistem.pengaturan', ['tab' => 'lokasi'])],
        [$data['pengampu_kosong'] === 0, 'Guru pengampu', $data['pengampu_kosong'] ? $data['pengampu_kosong'].' mapel belum punya guru' : 'Semua mapel sudah ada pengampunya', route('master.jadwal.index')],
        [count($data['rombel_kosong']) === 0, 'Siswa per rombel', count($data['rombel_kosong']) ? count($data['rombel_kosong']).' rombel belum berisi siswa' : 'Semua rombel berisi siswa', route('master.kelas')],
    ];
    $beres = collect($cekList)->where(0, true)->count();

    $aksiCepat = [
        ['Surat masuk', 'surat-masuk', route('administrasi.surat-masuk.create')],
        ['Surat keluar', 'surat-keluar', route('administrasi.surat-keluar.create')],
        ['Tambah siswa', 'siswa', route('master.siswa.create')],
        ['Data guru', 'guru', route('master.guru')],
        ['Jadwal', 'jadwal', route('master.jadwal.index')],
        ['Pengguna', 'pengguna', route('sistem.user')],
    ];
@endphp

<div wire:poll.60s class="space-y-5 font-body">
    <x-slot name="header">Dashboard Tata Usaha</x-slot>

    {{-- Sapaan + ringkasan data --}}
    <section class="relative overflow-hidden rounded-3xl bg-console p-5 text-white sm:p-7">
        <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="" aria-hidden="true" class="pointer-events-none absolute -right-10 -bottom-14 h-60 w-60 object-contain opacity-[0.06]">
        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm text-white/60">{{ $sapaan }}, {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }}</p>
                <h1 class="mt-1 font-heading text-2xl font-semibold leading-tight sm:text-3xl">Ruang kerja Tata Usaha</h1>
                <p class="mt-1.5 text-[13px] text-white/55">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · {{ $data['tahun_ajaran'] ?? 'Tahun ajaran belum aktif' }}</p>
            </div>
            <dl class="grid grid-cols-4 gap-2 sm:gap-3">
                @foreach ($data['jumlah'] as $label => $n)
                    <div class="rounded-2xl bg-white/[0.07] px-3 py-2.5 ring-1 ring-white/10 sm:px-4">
                        <dt class="text-[11px] font-medium text-white/50">{{ $label }}</dt>
                        <dd class="font-heading text-lg font-semibold tabular-nums sm:text-xl">{{ number_format($n, 0, ',', '.') }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- Quick info --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-ui.stat tone="navy" label="Surat masuk baru" :nilai="$surat['masuk_baru']" ikon="surat-masuk"
                   :sub="'Belum didisposisi · '.$surat['masuk_bulan_ini'].' surat bulan ini'" :href="route('administrasi.surat-masuk.index')" />
        <x-ui.stat label="Surat keluar" :nilai="$surat['keluar_draft'] + $surat['keluar_menunggu']" ikon="surat-keluar"
                   :sub="$surat['keluar_draft'].' draft/revisi · '.$surat['keluar_menunggu'].' menunggu persetujuan'" :href="route('administrasi.surat-keluar.index')" />
        <x-ui.stat label="Pegawai hadir" :nilai="$guru['hadir'] + $guru['terlambat'] + $tendik['hadir'] + $tendik['terlambat']" :satuan="'/ '.($guru['total'] + $tendik['total'])" ikon="guru"
                   :persen="($guru['total'] + $tendik['total']) ? (int) round(($guru['hadir'] + $guru['terlambat'] + $tendik['hadir'] + $tendik['terlambat']) / ($guru['total'] + $tendik['total']) * 100) : 0"
                   :sub="'Guru '.($guru['hadir'] + $guru['terlambat']).'/'.$guru['total'].' · Tendik '.($tendik['hadir'] + $tendik['terlambat']).'/'.$tendik['total']" :href="route('akademik.absensi.monitoring')" />
        <x-ui.stat label="Kehadiran siswa" :nilai="$persenSiswa ?? '—'" :satuan="$persenSiswa !== null ? '%' : null" ikon="siswa" :persen="$persenSiswa"
                   :sub="$siswa['tercatat'] ? $siswa['alfa'].' alfa dari '.$siswa['tercatat'].' tercatat' : 'Belum ada absensi kelas hari ini'" :href="route('akademik.absensi-siswa.rekap')" />
    </div>

    <div class="grid gap-5 xl:grid-cols-3">
        {{-- Antrian surat --}}
        <x-ui.panel class="xl:col-span-2" judul="Antrian surat masuk" keterangan="Surat berstatus baru, perlu diteruskan ke pimpinan">
            <x-slot:aksi>
                <a href="{{ route('administrasi.surat-masuk.create') }}" class="inline-flex h-8 items-center rounded-lg bg-navy-900 px-3 text-[12px] font-semibold text-white hover:bg-navy-800">+ Catat surat</a>
            </x-slot:aksi>
            @if ($surat['terbaru'])
                <div class="-mx-4 overflow-x-auto sm:-mx-5">
                    <table class="w-full text-left text-[13px]">
                        <thead class="text-[11px] uppercase tracking-wider text-navy-400">
                            <tr><th class="px-4 pb-2 font-semibold sm:px-5">No. agenda</th><th class="px-2 pb-2 font-semibold">Perihal</th><th class="px-4 pb-2 text-right font-semibold sm:px-5">Diterima</th></tr>
                        </thead>
                        <tbody class="divide-y divide-ivory-300">
                            @foreach ($surat['terbaru'] as $s)
                                <tr class="hover:bg-ivory-100">
                                    <td class="whitespace-nowrap px-4 py-2.5 font-mono text-[12px] text-navy-500 sm:px-5">{{ $s['no'] ?: '—' }}</td>
                                    <td class="max-w-0 px-2 py-2.5"><a href="{{ route('administrasi.surat-masuk.show', $s['id']) }}" class="block truncate font-semibold text-navy-900 hover:underline">{{ $s['perihal'] }}</a></td>
                                    <td class="whitespace-nowrap px-4 py-2.5 text-right text-navy-500 sm:px-5">{{ $s['tanggal'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex flex-col items-center py-8 text-center">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-ok-50 text-ok-500"><x-ui.icon name="surat-masuk" /></span>
                    <p class="mt-3 text-[14px] font-semibold text-navy-800">Antrian kosong</p>
                    <p class="mt-0.5 text-[12px] text-navy-400">Semua surat masuk sudah diteruskan.</p>
                </div>
            @endif
        </x-ui.panel>

        {{-- Kesiapan konfigurasi --}}
        <x-ui.panel judul="Kesiapan sistem" :keterangan="$beres.' dari '.count($cekList).' pengaturan beres'">
            <div class="mb-3 h-1.5 overflow-hidden rounded-full bg-ivory-300"><div class="h-full rounded-full {{ $beres === count($cekList) ? 'bg-ok-400' : 'bg-gold-400' }}" style="width: {{ $beres / count($cekList) * 100 }}%"></div></div>
            <ul class="space-y-1">
                @foreach ($cekList as [$ok, $judul, $ket, $url])
                    <li>
                        <a href="{{ $url }}" class="flex items-start gap-3 rounded-xl p-2 hover:bg-ivory-100">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full {{ $ok ? 'bg-ok-400 text-white' : 'bg-warn-50 text-warn-500' }}">
                                @if ($ok)<svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@else<span class="text-[11px] font-bold">!</span>@endif
                            </span>
                            <span class="min-w-0">
                                <span class="block text-[13px] font-semibold text-navy-900">{{ $judul }}</span>
                                <span class="block text-[12px] {{ $ok ? 'text-navy-400' : 'text-warn-500' }}">{{ $ket }}</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
            @if (count($data['rombel_kosong']))
                <p class="mt-2 text-[12px] text-navy-400">Rombel kosong: {{ implode(', ', array_slice($data['rombel_kosong'], 0, 6)) }}{{ count($data['rombel_kosong']) > 6 ? '…' : '' }}</p>
            @endif
        </x-ui.panel>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        {{-- Aksi cepat --}}
        <x-ui.panel judul="Aksi cepat" keterangan="Tekan Ctrl + K untuk mencari menu lain">
            <div class="grid grid-cols-3 gap-2">
                @foreach ($aksiCepat as [$label, $ikon, $url])
                    <a href="{{ $url }}" class="flex flex-col items-center gap-1.5 rounded-xl bg-ivory-100 px-2 py-3 text-center ring-1 ring-ivory-300 hover:bg-white hover:ring-navy-200">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-navy-700 ring-1 ring-ivory-300"><x-ui.icon :name="$ikon" class="h-5 w-5" /></span>
                        <span class="text-[12px] font-semibold leading-tight text-navy-800">{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        </x-ui.panel>

        {{-- Absensi pegawai hari ini --}}
        <x-ui.panel judul="Absensi pegawai hari ini" keterangan="Masuk · status per kategori">
            @foreach (['Guru' => $guru, 'Tendik' => $tendik] as $nama => $p)
                <div class="{{ $loop->first ? '' : 'mt-4' }}">
                    <div class="flex items-baseline justify-between text-[13px]"><span class="font-semibold text-navy-900">{{ $nama }}</span><span class="tabular-nums text-navy-500">{{ $p['persen'] }}%</span></div>
                    @php $t = max(1, $p['total']); @endphp
                    <div class="mt-1.5 flex h-2.5 overflow-hidden rounded-full bg-ivory-300">
                        <div class="bg-ok-400" style="width: {{ $p['hadir'] / $t * 100 }}%"></div>
                        <div class="bg-gold-400" style="width: {{ $p['terlambat'] / $t * 100 }}%"></div>
                        <div class="bg-info-500" style="width: {{ $p['izin'] / $t * 100 }}%"></div>
                    </div>
                    <p class="mt-1.5 text-[12px] text-navy-500">{{ $p['hadir'] }} tepat · {{ $p['terlambat'] }} terlambat · {{ $p['izin'] }} izin · <span class="{{ $p['belum'] ? 'font-semibold text-bad-500' : '' }}">{{ $p['belum'] }} belum</span></p>
                </div>
            @endforeach
            <div class="mt-4 flex gap-3 text-[11px] text-navy-400">
                <span class="flex items-center gap-1"><i class="h-2 w-2 rounded-full bg-ok-400"></i>Tepat</span>
                <span class="flex items-center gap-1"><i class="h-2 w-2 rounded-full bg-gold-400"></i>Terlambat</span>
                <span class="flex items-center gap-1"><i class="h-2 w-2 rounded-full bg-info-500"></i>Izin</span>
            </div>
        </x-ui.panel>

        {{-- KBM & rekap --}}
        <x-ui.panel judul="KBM & laporan" :keterangan="$kbm['slot'] ? 'Jam ke-'.$kbm['slot']['jam_ke'].' sedang berlangsung' : 'Di luar jam pelajaran'">
            <div class="grid grid-cols-2 gap-2">
                <div class="rounded-xl bg-ivory-100 p-3 ring-1 ring-ivory-300">
                    <p class="text-[11px] font-medium text-navy-400">Kelas aktif</p>
                    <p class="font-heading text-xl font-semibold tabular-nums text-navy-900">{{ count($kbm['berlangsung']) }}</p>
                </div>
                <div class="rounded-xl bg-ivory-100 p-3 ring-1 ring-ivory-300">
                    <p class="text-[11px] font-medium text-navy-400">Jurnal terisi</p>
                    <p class="font-heading text-xl font-semibold tabular-nums text-navy-900">{{ $kbm['jurnal_terisi'] }}<span class="text-sm font-medium text-navy-300">/{{ $kbm['jurnal_wajib'] }}</span></p>
                </div>
            </div>
            <p class="mt-4 text-[11px] font-semibold uppercase tracking-wider text-navy-400">Unduh rekap {{ now()->locale('id')->isoFormat('MMMM') }}</p>
            <div class="mt-2 space-y-1.5">
                @foreach (['guru' => 'Rekap kehadiran guru', 'tendik' => 'Rekap kehadiran tendik'] as $k => $l)
                    <a href="{{ route('akademik.absensi.rekap-bulanan', ['kategori' => $k, 'bulan' => now()->format('Y-m')]) }}" class="flex items-center justify-between rounded-xl px-3 py-2 text-[13px] font-semibold text-navy-800 ring-1 ring-ivory-300 hover:bg-ivory-100">
                        {{ $l }} <span class="text-[11px] font-medium text-navy-400">.xlsx ↓</span>
                    </a>
                @endforeach
                <a href="{{ route('laporan.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2 text-[13px] font-semibold text-navy-800 ring-1 ring-ivory-300 hover:bg-ivory-100">Pusat laporan (siswa, jurnal, dll.) <span class="text-navy-400">›</span></a>
            </div>
        </x-ui.panel>
    </div>
</div>
