@php
    $jam = (int) now()->format('H');
    $sapaan = match (true) { $jam < 11 => 'Selamat pagi', $jam < 15 => 'Selamat siang', $jam < 18 => 'Selamat sore', default => 'Selamat malam' };
    $persenJurnal = $kbm['jurnal_wajib'] ? (int) round($kbm['jurnal_terisi'] / $kbm['jurnal_wajib'] * 100) : null;
    $persenSiswa = $siswa['tercatat'] ? (int) round($siswa['hadir'] / $siswa['tercatat'] * 100) : null;
@endphp

<div wire:poll.60s class="space-y-5 font-body">
    <x-slot name="header">Dashboard Kepala Sekolah</x-slot>

    {{-- Sapaan + aksi --}}
    <section class="relative overflow-hidden rounded-3xl bg-console p-5 text-white sm:p-7">
        <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="" aria-hidden="true" class="pointer-events-none absolute -right-10 -bottom-14 h-60 w-60 object-contain opacity-[0.06]">
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm text-white/60">{{ $sapaan }}, {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }}</p>
                <h1 class="mt-1 font-heading text-2xl font-semibold leading-tight sm:text-3xl">Pemantauan sekolah hari ini</h1>
                <p class="mt-1.5 text-[13px] text-white/55">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · diperbarui otomatis tiap menit</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('akademik.absensi.rekap-bulanan', ['kategori' => 'guru', 'bulan' => now()->format('Y-m')]) }}"
                   class="inline-flex h-10 items-center gap-2 rounded-xl bg-gold-400 px-4 text-[13px] font-semibold text-navy-950 hover:bg-gold-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    Rekap guru bulan ini
                </a>
                <a href="{{ route('akademik.absensi.rekap-bulanan', ['kategori' => 'tendik', 'bulan' => now()->format('Y-m')]) }}"
                   class="inline-flex h-10 items-center gap-2 rounded-xl px-4 text-[13px] font-semibold text-white ring-1 ring-white/25 hover:bg-white/10">Rekap tendik</a>
            </div>
        </div>
    </section>

    {{-- Quick info --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-ui.stat tone="navy" label="Guru hadir" :nilai="$guru['hadir'] + $guru['terlambat']" :satuan="'/ '.$guru['total']" ikon="guru" :persen="$guru['persen']"
                   :sub="$guru['terlambat'].' terlambat · '.$guru['izin'].' izin/sakit · '.$guru['belum'].' belum absen'" :href="route('akademik.absensi.monitoring')" />
        <x-ui.stat label="Tendik hadir" :nilai="$tendik['hadir'] + $tendik['terlambat']" :satuan="'/ '.$tendik['total']" ikon="tendik" :persen="$tendik['persen']"
                   :sub="$tendik['belum'].' belum absen'" :href="route('akademik.absensi.tendik.monitoring')" />
        <x-ui.stat label="Kehadiran siswa" :nilai="$persenSiswa !== null ? $persenSiswa : '—'" :satuan="$persenSiswa !== null ? '%' : null" ikon="siswa" :persen="$persenSiswa"
                   :sub="$siswa['tercatat'] ? ($siswa['alfa'].' alfa · '.$siswa['izin'].' izin/sakit dari '.$siswa['tercatat'].' tercatat') : 'Belum ada absensi kelas hari ini'" />
        <x-ui.stat label="Jurnal terisi" :nilai="$kbm['jurnal_wajib'] ? $kbm['jurnal_terisi'] : '—'" :satuan="$kbm['jurnal_wajib'] ? '/ '.$kbm['jurnal_wajib'] : null" ikon="jurnal" :persen="$persenJurnal"
                   :sub="$kbm['jurnal_wajib'] ? 'Dari jam pelajaran yang sudah selesai' : 'Belum ada jam pelajaran yang selesai'" :href="route('supervisi.jurnal.rekapan')" />
    </div>

    <div class="grid gap-5 xl:grid-cols-3">
        {{-- KBM berlangsung --}}
        <x-ui.panel class="xl:col-span-2" judul="KBM sedang berlangsung"
                    :keterangan="$kbm['slot'] ? 'Jam ke-'.$kbm['slot']['jam_ke'].' · '.$kbm['slot']['mulai'].'–'.$kbm['slot']['selesai'].' WITA' : 'Di luar jam pelajaran'">
            <x-slot:aksi>
                <a href="{{ route('supervisi.jurnal.realtime') }}" class="text-[13px] font-semibold text-navy-600 hover:text-navy-900">Supervisi realtime ›</a>
            </x-slot:aksi>
            @if ($kbm['berlangsung'])
                <ul class="grid gap-2.5 sm:grid-cols-2">
                    @foreach ($kbm['berlangsung'] as $s)
                        <li class="rounded-xl bg-ivory-100 p-3.5 ring-1 ring-ivory-300">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-[12px] font-semibold text-navy-400">{{ $s['kelas'] }}</p>
                                    <p class="truncate text-[14px] font-semibold text-navy-900">{{ $s['mapel'] }}</p>
                                    <p class="truncate text-[12px] text-navy-500">{{ $s['guru'] ?? ($s['kegiatan'] ? 'Kegiatan sekolah' : 'Guru belum diatur') }}</p>
                                </div>
                                @unless ($s['kegiatan'])
                                    <div class="flex shrink-0 flex-col items-end gap-1">
                                        <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $s['guru_hadir'] ? 'bg-ok-50 text-ok-500' : 'bg-bad-50 text-bad-500' }}">{{ $s['guru_hadir'] ? 'Guru hadir' : 'Guru belum absen' }}</span>
                                        <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $s['jurnal'] ? 'bg-ok-50 text-ok-500' : 'bg-ivory-200 text-navy-500' }}">{{ $s['jurnal'] ? 'Jurnal terisi' : 'Jurnal belum' }}</span>
                                    </div>
                                @endunless
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-ivory-200 text-navy-400"><x-ui.icon name="jadwal" /></span>
                    <p class="mt-3 text-[14px] font-semibold text-navy-800">Tidak ada jam pelajaran saat ini</p>
                    <p class="mt-0.5 text-[12px] text-navy-400">Istirahat, kegiatan, atau di luar jam sekolah.</p>
                </div>
            @endif
        </x-ui.panel>

        {{-- Tren kehadiran guru --}}
        <x-ui.panel judul="Tren kehadiran guru" keterangan="7 hari kerja terakhir">
            <div class="flex h-40 items-end gap-2" role="img" aria-label="Grafik persentase kehadiran guru 7 hari kerja terakhir">
                @foreach ($tren as $t)
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-1.5">
                        <span class="text-[11px] font-semibold tabular-nums text-navy-600">{{ $t['persen'] }}%</span>
                        <div class="w-full rounded-t-lg {{ $t['hari_ini'] ? 'bg-gold-400' : 'bg-navy-800' }}" style="height: {{ max(4, $t['persen']) }}%"></div>
                        <span class="text-[11px] {{ $t['hari_ini'] ? 'font-semibold text-navy-900' : 'text-navy-400' }}">{{ $t['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </x-ui.panel>
    </div>

    {{-- Perlu perhatian --}}
    <div class="grid gap-5 lg:grid-cols-3">
        <x-ui.panel judul="Guru belum absen" :keterangan="$guru['belum'].' dari '.$guru['total'].' guru'">
            @forelse ($belumAbsen as $nama)
                <p class="flex items-center gap-2.5 border-b border-dashed border-ivory-300 py-2 text-[14px] text-navy-800 last:border-0"><span class="h-2 w-2 rounded-full bg-bad-400"></span>{{ $nama }}</p>
            @empty
                <p class="py-6 text-center text-[13px] font-medium text-ok-500">Semua guru sudah absen ✓</p>
            @endforelse
            @if ($guru['belum'] > count($belumAbsen))
                <a href="{{ route('akademik.absensi.monitoring') }}" class="mt-2 inline-block text-[13px] font-semibold text-navy-600">Lihat semua ›</a>
            @endif
        </x-ui.panel>

        <x-ui.panel judul="Siswa alfa berulang" keterangan="≥ 3 hari alfa bulan ini">
            @forelse ($alfa as $a)
                <div class="flex items-center justify-between gap-2 border-b border-dashed border-ivory-300 py-2 last:border-0">
                    <div class="min-w-0"><p class="truncate text-[14px] font-semibold text-navy-800">{{ $a['nama'] }}</p><p class="text-[12px] text-navy-400">{{ $a['kelas'] }}</p></div>
                    <span class="shrink-0 rounded-full bg-bad-50 px-2.5 py-0.5 text-[12px] font-semibold text-bad-500">{{ $a['hari'] }} hari</span>
                </div>
            @empty
                <p class="py-6 text-center text-[13px] font-medium text-ok-500">Tidak ada siswa alfa berulang ✓</p>
            @endforelse
        </x-ui.panel>

        <x-ui.panel judul="Surat menunggu tindak lanjut" :keterangan="$surat['masuk_baru'].' surat masuk belum didisposisi'">
            @forelse ($surat['terbaru'] as $s)
                <a href="{{ route('administrasi.surat-masuk.show', $s['id']) }}" class="flex items-start justify-between gap-2 border-b border-dashed border-ivory-300 py-2 last:border-0 hover:text-navy-900">
                    <span class="min-w-0"><span class="block truncate text-[14px] font-semibold text-navy-800">{{ $s['perihal'] }}</span><span class="text-[12px] text-navy-400">{{ $s['no'] }}</span></span>
                    <span class="shrink-0 text-[12px] text-navy-400">{{ $s['tanggal'] }}</span>
                </a>
            @empty
                <p class="py-6 text-center text-[13px] font-medium text-ok-500">Tidak ada surat tertunda ✓</p>
            @endforelse
            @if ($surat['keluar_menunggu'])
                <p class="mt-3 rounded-xl bg-warn-50 px-3 py-2 text-[12px] font-semibold text-warn-500">{{ $surat['keluar_menunggu'] }} surat keluar menunggu persetujuan</p>
            @endif
        </x-ui.panel>
    </div>
</div>
