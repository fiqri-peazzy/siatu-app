<?php

/** Definisi menu panel admin — dipakai sidebar & command palette (Ctrl/Cmd+K). Item tanpa route = "Segera". */
return (function () {
    $u = auth()->user();
    $admin = ['admin-tu', 'super-admin'];
    $pimpinan = ['admin-tu', 'kepala-sekolah', 'super-admin'];
    $pemantau = [...$pimpinan, 'staf-tu']; // monitoring absensi & rekap siswa
    $tu = [...$admin, 'staf-tu'];

    $grup = [
        null => [
            ['Dashboard', 'dashboard', 'dashboard', 'dashboard', null],
        ],
        'Administrasi' => [
            ['Surat Masuk', 'administrasi.surat-masuk.index', 'administrasi.surat-masuk.*', 'surat-masuk', null],
            ['Surat Keluar', 'administrasi.surat-keluar.index', 'administrasi.surat-keluar.*', 'surat-keluar', null],
        ],
        'Akademik' => [
            ['Absen Sekarang', 'akademik.absensi.guru.form', 'akademik.absensi.guru.form', 'absen', ['guru']],
            ['Riwayat Absensi', 'akademik.absensi.guru.riwayat', 'akademik.absensi.guru.riwayat', 'riwayat', ['guru']],
            ['Monitoring Absen Guru', 'akademik.absensi.monitoring', 'akademik.absensi.monitoring', 'monitor', $pemantau],
            ['Monitoring Absen Tendik', 'akademik.absensi.tendik.monitoring', 'akademik.absensi.tendik.monitoring', 'monitor', $pemantau],
            ['Rekap Absen Siswa', 'akademik.absensi-siswa.rekap', 'akademik.absensi-siswa.rekap', 'rekap', $pemantau],
            ['Peminatan', 'akademik.peminatan.index', 'akademik.peminatan.index', 'peminatan', $admin],
            ['Jurnal Mengajar', 'supervisi.jurnal.rekapan', 'supervisi.jurnal.*', 'jurnal', $pimpinan],
            ['Pusat Laporan', 'laporan.index', 'laporan.*', 'rekap', $pimpinan],
        ],
        'Manajemen' => [
            ['Sapras & Aset', null, null, 'aset', null],
            ['Dana BOS', null, null, 'bos', null],
            ['Kegiatan', null, null, 'kegiatan', null],
        ],
        'Data Master' => [
            ['Tahun Ajaran', 'master.tahun-ajaran', 'master.tahun-ajaran', 'tahun-ajaran', $admin],
            ['Guru', 'master.guru', 'master.guru', 'guru', $admin],
            ['Tendik', 'master.tendik', 'master.tendik', 'tendik', $admin],
            ['Mapel', 'master.mapel', 'master.mapel', 'mapel', $admin],
            ['Kelas / Rombel', 'master.kelas', 'master.kelas', 'kelas', $admin],
            ['Siswa', 'master.siswa', ['master.siswa', 'master.siswa.create', 'master.siswa.edit'], 'siswa', $admin],
            ['Pindah Rombel', 'master.siswa.pindah-rombel', 'master.siswa.pindah-rombel', 'kelas', $tu],
            ['Jadwal Pelajaran', 'master.jadwal.index', 'master.jadwal.*', 'jadwal', $admin],
        ],
        'Sistem' => [
            ['Pengaturan Sistem', 'sistem.pengaturan', 'sistem.pengaturan', 'pengaturan', $admin],
            ['Pengguna', 'sistem.user', 'sistem.user', 'pengguna', $admin],
        ],
    ];

    return $grup;
})();
