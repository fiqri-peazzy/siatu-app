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

    'nama'        => env('SEKOLAH_NAMA', 'SMA Negeri 1 Contoh'),
    'npsn'        => env('SEKOLAH_NPSN', ''),
    'alamat'      => env('SEKOLAH_ALAMAT', ''),
    'kota'        => env('SEKOLAH_KOTA', ''),
    'provinsi'    => env('SEKOLAH_PROVINSI', ''),
    'telepon'     => env('SEKOLAH_TELEPON', ''),
    'email'       => env('SEKOLAH_EMAIL', ''),
    'logo_path'   => env('SEKOLAH_LOGO', 'logo/logo-sekolah.png'),
    
    // Pejabat berwenang
    'kepala'      => env('SEKOLAH_KEPALA_NAMA', 'Nama Kepala Sekolah, S.Pd.'),
    'nip_kepala'  => env('SEKOLAH_KEPALA_NIP', ''),
    'kepala_tu'   => env('SEKOLAH_KA_TU_NAMA', ''),
    'nip_ka_tu'   => env('SEKOLAH_KA_TU_NIP', ''),
];
