<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Data Identitas Sekolah
    |--------------------------------------------------------------------------
    |
    | Digunakan untuk kop surat, laporan PDF, dan identitas aplikasi.
    |
    */

    // Baris kop di atas nama sekolah (pisahkan baris dengan |)
    'induk'       => env('SEKOLAH_INDUK', 'MAJELIS PENDIDIKAN DASAR DAN MENENGAH|PIMPINAN DAERAH MUHAMMADIYAH KOTA GORONTALO'),
    'nama'        => env('SEKOLAH_NAMA') ?: 'SMA Muhammadiyah Kota Gorontalo',
    'tagline'     => env('SEKOLAH_TAGLINE') ?: 'Islamic Global School',
    'akreditasi'  => env('SEKOLAH_AKREDITASI', ''),
    'npsn'        => env('SEKOLAH_NPSN', ''),
    // ?: agar nilai kosong di .env tetap jatuh ke data resmi sekolah
    'alamat'      => env('SEKOLAH_ALAMAT') ?: 'Jln. Taman Bunga, Kota Timur',
    'kota'        => env('SEKOLAH_KOTA') ?: 'Kota Gorontalo',
    'provinsi'    => env('SEKOLAH_PROVINSI') ?: 'Gorontalo',
    'telepon'     => env('SEKOLAH_TELEPON', ''),
    'email'       => env('SEKOLAH_EMAIL') ?: 'smasmuhammadiyahkotagorontalo@gmail.com',
    'logo_path'   => env('SEKOLAH_LOGO', 'logo/logo-sekolah.png'),
    
    // Pejabat berwenang
    'kepala'      => env('SEKOLAH_KEPALA_NAMA', 'Nama Kepala Sekolah, S.Pd.'),
    'nip_kepala'  => env('SEKOLAH_KEPALA_NIP', ''),
    'kepala_tu'   => env('SEKOLAH_KA_TU_NAMA', ''),
    'nip_ka_tu'   => env('SEKOLAH_KA_TU_NIP', ''),
];
