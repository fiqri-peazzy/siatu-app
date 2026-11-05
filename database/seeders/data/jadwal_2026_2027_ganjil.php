<?php

/**
 * Jadwal Pelajaran Semester Ganjil TP 2026/2027 — SMAMGo Islamic Global School
 * Transkripsi dari foto dokumen resmi (ditandatangani Kepala Sekolah & Wakasek Kurikulum, Juli 2026).
 *
 * ⚠ PERLU VERIFIKASI: foto diputar & beresolusi rendah. Baris bertanda "⚠" paling meragukan.
 *
 * FORMAT SLOT
 *   [jam_ke, mulai, selesai, isi]
 *   jam_ke = null → slot non-pelajaran; isi = ['upacara'|'istirahat', 'Label']
 *
 * FORMAT ISI (kunci = target rombel)
 *   'X'      → sesi gabungan X-SCI + X-SOC   (sama untuk 'XI', 'XII')
 *   'X-SCI'  → hanya satu rombel
 *   'ALL'    → semua rombel
 * NILAI
 *   'FIS'            → satu mapel (kode singkat, lihat 'mapel' di bawah)
 *   'BIO|MATL|BINGTL'→ mapel pilihan paralel (peserta dari peminatan siswa)
 *   '#Label'         → kegiatan tanpa mapel
 */

$istirahat = [null, '09:45', '10:15', ['istirahat', 'Istirahat / Shalat Dhuha']];
$ishoma = [null, '11:45', '13:00', ['istirahat', 'ISHOMA']];

$x = fn (string $sci, string $soc) => ['X-SCI' => $sci, 'X-SOC' => $soc];
$xi = fn (string $sci, string $soc) => ['XI-SCI' => $sci, 'XI-SOC' => $soc];

return [
    'tahun_ajaran' => ['nama' => '2026/2027', 'semester' => 'Ganjil'],
    'pola_jam' => 'Pola Jam Semester Ganjil 2026/2027',

    'rombel' => [
        // kode => [nama, tingkat, jurusan]
        'X-SCI' => ['X Science', 'X', 'IPA'],
        'X-SOC' => ['X Social', 'X', 'IPS'],
        'XI-SCI' => ['XI Science', 'XI', 'IPA'],
        'XI-SOC' => ['XI Social', 'XI', 'IPS'],
        'XII-SCI' => ['XII Science', 'XII', 'IPA'],
        'XII-SOC' => ['XII Social', 'XII', 'IPS'],
    ],

    'mapel' => [
        // singkatan di jadwal => [kode master_mapels, nama, kelompok]
        // Kode MPL-xxx / BING-TKA / dst. sudah ada di production → dipakai ulang.
        'PAI' => ['MPL-001', 'Pendidikan Agama Islam', 'Umum'],
        'PKN' => ['MPL-002', 'Pendidikan Pancasila', 'Umum'],
        'BIND' => ['MPL-003', 'Bahasa Indonesia', 'Umum'],
        'MAT' => ['MPL-004', 'Matematika', 'Umum'],
        'BING' => ['MPL-005', 'Bahasa Inggris', 'Umum'],
        'INF' => ['MPL-007', 'Informatika', 'Umum'],
        'FIS' => ['MPL-008', 'Fisika', 'IPA'],
        'KIM' => ['MPL-009', 'Kimia', 'IPA'],
        'BIO' => ['MPL-010', 'Biologi', 'IPA'],
        'SEJ' => ['MPL-011', 'Sejarah', 'IPS'],
        'SOS' => ['MPL-012', 'Sosiologi', 'IPS'],
        'EKO' => ['MPL-013', 'Ekonomi', 'IPS'],
        'GEO' => ['MPL-014', 'Geografi', 'IPS'],
        'PKWU' => ['MPL-019', 'Prakarya dan Kewirausahaan', 'Seni & Prakarya'],
        'BARAB' => ['MPL-020', 'Bahasa Arab', 'Muatan Lokal'],
        'ANT' => ['ANTROPOLOGI', 'Antropologi', 'IPS'],
        'OPSI' => ['OPSI', 'OPSI', null],
        'TAHFIDZ' => ['TAHFIDZ', 'Tahfidz', 'Muatan Lokal'],
        'BING-TKA' => ['BING-TKA', 'Bahasa Inggris (TKA)', 'TKA'],
        'BIND-TKA' => ['BIND-TKA', 'Bahasa Indonesia (TKA)', 'TKA'],
        'MAT-TKA' => ['MAT-TKA', 'Matematika (TKA)', 'TKA'],
        // Baru — ⚠ konfirmasi kepanjangan singkatan
        'BINTL' => ['BIND-TL', 'Bahasa Indonesia Tingkat Lanjut', 'Pilihan'],
        'BINGTL' => ['BING-TL', 'Bahasa Inggris Tingkat Lanjut', 'Pilihan'],
        'BATL' => ['BARAB-TL', 'Bahasa Arab Tingkat Lanjut', 'Pilihan'],
        'MATL' => ['MAT-TL', 'Matematika Tingkat Lanjut', 'Pilihan'],
    ],

    'hari' => [
        'senin' => [
            [null, '07:00', '07:15', ['upacara', 'Upacara Bendera']], // ⚠ foto: 07.00–07.45, bentrok dgn jam ke-I
            [1, '07:15', '07:45', ['X' => 'PAI', 'XI' => 'PKN', 'XII' => 'BING-TKA']],  // ⚠
            [2, '07:45', '08:15', ['X' => 'PKN', 'XI' => 'BIND', 'XII' => 'BING-TKA']], // ⚠
            [3, '08:15', '08:45', ['X' => 'BIND', 'XI' => 'PAI', 'XII' => 'BIO|MATL|BINGTL']], // ⚠
            [4, '08:45', '09:15', $x('FIS', 'EKO') + $xi('INF', 'SEJ') + ['XII' => 'BIO|MATL|BINGTL']],
            [5, '09:15', '09:45', $x('FIS', 'EKO') + $xi('INF', 'SEJ') + ['XII' => 'PAI']],
            $istirahat,
            [6, '10:15', '10:45', $x('FIS', 'EKO') + $xi('INF', 'SEJ') + ['XII' => 'PKN']],
            [7, '10:45', '11:15', $x('BIO', 'SEJ') + $xi('MAT', 'SOS') + ['XII' => 'BIND-TKA']],
            [8, '11:15', '11:45', $x('BIO', 'SEJ') + $xi('MAT', 'SOS') + ['XII' => 'BIND-TKA']],
            $ishoma,
            [9, '13:00', '13:30', $x('BIO', 'SEJ') + $xi('MAT', 'SOS') + ['XII' => 'BINTL|BATL|INF']],
            [10, '13:30', '14:00', $x('INF', 'SOS') + $xi('BIO', 'GEO') + ['XII' => 'BINTL|BATL|INF']],
            [11, '14:00', '14:30', $x('INF', 'SOS') + $xi('BIO', 'GEO') + ['XII' => 'MAT-TKA']],
            [12, '14:30', '15:00', $x('INF', 'SOS') + $xi('BIO', 'GEO') + ['XII' => 'MAT-TKA']],
        ],

        'selasa' => [
            [1, '07:15', '07:45', ['X' => 'TAHFIDZ', 'XI' => 'BARAB', 'XII' => 'BING']],
            [2, '07:45', '08:15', ['X' => 'TAHFIDZ', 'XI' => 'BARAB', 'XII' => 'BING']],
            [3, '08:15', '08:45', $x('EKO', 'FIS') + $xi('BINTL', 'ANT') + ['XII' => 'MAT-TKA']], // ⚠ urutan Sci/Soc
            [4, '08:45', '09:15', $x('SEJ', 'KIM') + $xi('BINTL', 'ANT') + ['XII' => 'MAT-TKA']], // ⚠
            [5, '09:15', '09:45', $x('GEO', 'BIO') + $xi('OPSI', 'ANT') + ['XII' => 'KIM|FIS|SOS|EKO']], // ⚠
            $istirahat,
            [6, '10:15', '10:45', $x('MAT', 'GEO') + $xi('OPSI', 'EKO') + ['XII' => 'KIM|FIS|SOS|EKO']],
            [7, '10:45', '11:15', $x('MAT', 'GEO') + $xi('OPSI', 'EKO') + ['XII' => 'BIND-TKA']],
            [8, '11:15', '11:45', $x('MAT', 'GEO') + $xi('OPSI', 'EKO') + ['XII' => 'BIND-TKA']],
            $ishoma,
            [9, '13:00', '13:30', $x('KIM', 'ANT') + $xi('FIS', 'BINTL') + ['XII' => 'BIO|MATL|BINGTL']],
            [10, '13:30', '14:00', $x('KIM', 'ANT') + $xi('FIS', 'BINTL') + ['XII' => 'BIO|MATL|BINGTL']],
        ],

        'rabu' => [
            [1, '07:15', '07:45', ['X' => 'BING', 'XI' => 'TAHFIDZ', 'XII' => 'BARAB']],
            [2, '07:45', '08:15', ['X' => 'BING', 'XI' => 'TAHFIDZ', 'XII' => 'BARAB']],
            [3, '08:15', '08:45', $x('BINTL', 'MAT') + $xi('FIS', 'INF') + ['XII' => 'BING-TKA']],
            [4, '08:45', '09:15', $x('BINTL', 'MAT') + $xi('FIS', 'INF') + ['XII' => 'BING-TKA']],
            [5, '09:15', '09:45', $x('SOS', 'BINTL') + $xi('FIS', 'MAT') + ['XII' => 'KIM|FIS|SOS|EKO']],
            $istirahat,
            [6, '10:15', '10:45', $x('OPSI', 'BINTL') + $xi('KIM', 'MAT') + ['XII' => 'KIM|FIS|SOS|EKO']],
            [7, '10:45', '11:15', $x('OPSI', 'INF') + $xi('KIM', 'GEO') + ['XII' => 'PKWU']],
            [8, '11:15', '11:45', $x('OPSI', 'INF') + $xi('KIM', 'GEO') + ['XII' => 'PKWU']],
            $ishoma,
            [9, '13:00', '13:30', $x('KIM', 'ANT') + ['XI' => 'PKWU', 'XII' => 'BINTL|BATL|INF']],
            [10, '13:30', '14:00', $x('KIM', 'ANT') + ['XI' => 'PKWU', 'XII' => 'BINTL|BATL|INF']],
        ],

        'kamis' => [
            [1, '07:15', '07:45', ['X' => 'BARAB', 'XI' => 'BING', 'XII' => 'TAHFIDZ']],
            [2, '07:45', '08:15', ['X' => 'BARAB', 'XI' => 'BING', 'XII' => 'TAHFIDZ']],
            [3, '08:15', '08:45', ['ALL' => '#Pembinaan Ekstrakurikuler Olahraga']],
            [4, '08:45', '09:15', ['ALL' => '#Pembinaan Ekstrakurikuler Olahraga']],
            [5, '09:15', '09:45', ['ALL' => '#Pembinaan Ekstrakurikuler Olahraga']],
            $istirahat,
            [6, '10:15', '10:45', ['ALL' => '#Pembinaan Ekstrakurikuler Seni']],
            [7, '10:45', '11:15', ['ALL' => '#Pembinaan Ekstrakurikuler Seni']],
            [8, '11:15', '11:45', ['ALL' => '#Pembinaan Ekstrakurikuler Seni']],
            $ishoma,
            [9, '13:00', '13:30', ['ALL' => '#Pembinaan Ekstrakurikuler Seni']],
            [10, '13:30', '14:00', ['ALL' => '#Pembinaan Ekstrakurikuler Seni']],
        ],

        'jumat' => [
            [1, '07:15', '07:45', ['ALL' => '#Pembinaan ORTOM (Hizbul Wathan, IPM, Tapak Suci)']],
            [2, '07:45', '08:15', ['ALL' => '#Pembinaan ORTOM (Hizbul Wathan, IPM, Tapak Suci)']],
            [3, '08:15', '08:45', ['ALL' => '#Pembinaan ORTOM (Hizbul Wathan, IPM, Tapak Suci)']],
            [4, '08:45', '09:15', ['ALL' => '#Pembinaan ORTOM (Hizbul Wathan, IPM, Tapak Suci)']],
            [5, '09:15', '09:45', ['ALL' => '#Pembinaan ORTOM (Hizbul Wathan, IPM, Tapak Suci)']],
            $istirahat,
            [6, '10:15', '10:45', ['ALL' => '#Pembinaan ORTOM (Hizbul Wathan, IPM, Tapak Suci)']],
            [7, '10:45', '11:15', ['ALL' => '#Pembinaan ORTOM (Hizbul Wathan, IPM, Tapak Suci)']],
            [8, '11:15', '11:45', ['ALL' => '#Pembinaan ORTOM (Hizbul Wathan, IPM, Tapak Suci)']],
            $ishoma,
            [9, '13:00', '13:30', ['ALL' => '#Pembinaan ORTOM (Hizbul Wathan, IPM, Tapak Suci)']],
            [10, '13:30', '14:00', ['ALL' => '#Pembinaan ORTOM (Hizbul Wathan, IPM, Tapak Suci)']],
        ],
    ],
];
