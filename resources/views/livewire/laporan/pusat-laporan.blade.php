@php
    $laporan = [
        [
            'jenis' => 'kehadiran-guru', 'judul' => 'Kehadiran guru', 'ikon' => 'guru',
            'isi' => 'Matriks guru × tanggal (hadir, terlambat, izin, sakit, DL, alfa), hari libur abu-abu, persentase hadir.',
            'sheet' => ['Rekap Bulanan', 'Detail Harian (jam datang & pulang)'],
        ],
        [
            'jenis' => 'kehadiran-tendik', 'judul' => 'Kehadiran tendik', 'ikon' => 'tendik',
            'isi' => 'Format sama dengan rekap guru untuk tenaga kependidikan.',
            'sheet' => ['Rekap Bulanan', 'Detail Harian'],
        ],
        [
            'jenis' => 'absensi-siswa', 'judul' => 'Kehadiran siswa', 'ikon' => 'siswa',
            'isi' => 'Ringkasan semua rombel + daftar hadir per rombel (siswa × tanggal) dengan tanda tangan wali kelas.',
            'sheet' => ['Ringkasan per rombel', 'Satu sheet tiap rombel'],
            'kosong' => $info['absen_siswa'] === 0 ? 'Belum ada absensi kelas tercatat bulan ini.' : null,
        ],
        [
            'jenis' => 'jurnal', 'judul' => 'Jurnal mengajar', 'ikon' => 'jurnal',
            'isi' => 'Keterisian jurnal tiap guru dibanding pertemuan terjadwal, plus daftar materi yang diajarkan.',
            'sheet' => ['Rekap per Guru', 'Daftar Jurnal'],
            'kosong' => $info['jurnal'] === 0 ? 'Belum ada jurnal terisi bulan ini — rekap tetap menunjukkan yang belum diisi.' : null,
        ],
    ];
@endphp

<div class="space-y-5 font-body">
    <x-slot name="header">Pusat Laporan</x-slot>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="font-heading text-2xl font-semibold text-navy-900">Pusat Laporan</h1>
            <p class="mt-1 text-[13px] text-navy-500">Rekap bulanan siap cetak dalam Excel — berkop resmi dan bertanda tangan kepala sekolah.</p>
        </div>
        <x-ui.pilih-bulan :bulan="$filterBulan" :tahun="$filterTahun" class="w-full justify-between sm:w-auto" />
    </div>

    {{-- Info bulan --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-ui.stat tone="navy" label="Hari sekolah" :nilai="$info['hari_sekolah']" satuan="hari" ikon="jadwal"
                   :sub="$bulan->isSameMonth(now()) ? $info['hari_berjalan'].' hari sudah berjalan' : 'Bulan '.$bulan->locale('id')->isoFormat('MMMM YYYY')" />
        <x-ui.stat label="Hari absen siswa" :nilai="$info['absen_siswa']" satuan="hari" ikon="siswa" sub="Tanggal dengan absensi kelas tercatat" />
        <x-ui.stat label="Jurnal terisi" :nilai="number_format($info['jurnal'], 0, ',', '.')" satuan="jam" ikon="jurnal" sub="Jam pelajaran × rombel" />
        <x-ui.stat label="Libur nasional" :nilai="count($info['libur'])" satuan="hari" ikon="tahun-ajaran"
                   :sub="e($info['libur'] ? implode(' · ', array_slice($info['libur'], 0, 2)).(count($info['libur']) > 2 ? ' …' : '') : 'Tidak ada libur nasional')" />
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @foreach ($laporan as $l)
            <article class="flex flex-col rounded-2xl bg-white p-4 ring-1 ring-ivory-300 sm:p-5" wire:key="lap-{{ $l['jenis'] }}">
                <div class="flex items-start gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-ivory-200 text-navy-700"><x-ui.icon :name="$l['ikon']" class="h-5 w-5" /></span>
                    <div class="min-w-0">
                        <h2 class="font-heading text-[16px] font-semibold text-navy-900">Rekap {{ strtolower($l['judul']) }}</h2>
                        <p class="mt-0.5 text-[13px] leading-snug text-navy-500">{{ $l['isi'] }}</p>
                    </div>
                </div>
                <ul class="mt-3 flex flex-wrap gap-1.5">
                    @foreach ($l['sheet'] as $s)
                        <li class="rounded-full bg-ivory-100 px-2.5 py-1 text-[11px] font-semibold text-navy-600 ring-1 ring-ivory-300">{{ $s }}</li>
                    @endforeach
                </ul>
                @if (! empty($l['kosong']))
                    <p class="mt-3 rounded-xl bg-warn-50 px-3 py-2 text-[12px] font-medium text-warn-500">{{ $l['kosong'] }}</p>
                @endif
                <div class="mt-auto pt-4">
                    <a href="{{ route('laporan.unduh', ['jenis' => $l['jenis'], 'bulan' => $kodeBulan]) }}"
                       x-data="{ proses: false }" @click="proses = true; setTimeout(() => proses = false, 6000)"
                       class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-navy-900 px-4 text-[13px] font-semibold text-white hover:bg-navy-800 sm:w-auto">
                        <svg x-show="!proses" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                        <span x-show="proses" x-cloak class="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white" aria-hidden="true"></span>
                        <span x-text="proses ? 'Menyiapkan file…' : 'Unduh Excel {{ $bulan->locale('id')->isoFormat('MMM YYYY') }}'">Unduh Excel</span>
                    </a>
                </div>
            </article>
        @endforeach
    </div>

    <p class="text-[12px] text-navy-400">
        Kop & penandatangan diambil dari
        @if (auth()->user()->hasAnyRole(['admin-tu', 'super-admin']))
            <a href="{{ route('sistem.pengaturan') }}" class="font-semibold text-navy-600 underline-offset-2 hover:underline">Pengaturan Sistem → Identitas sekolah</a>.
        @else
            Pengaturan Sistem (diatur oleh Tata Usaha).
        @endif
    </p>
</div>
