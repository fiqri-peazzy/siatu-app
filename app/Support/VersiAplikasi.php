<?php

namespace App\Support;

/**
 * Sidik versi aplikasi = hash manifest build Vite. Berubah setiap deploy yang mengubah CSS/JS,
 * dipakai PWA untuk mendeteksi pembaruan (resources/js/pembaruan.js).
 */
class VersiAplikasi
{
    private static ?string $nilai = null;

    public static function nilai(): string
    {
        if (self::$nilai !== null) {
            return self::$nilai;
        }
        $manifest = public_path('build/manifest.json');
        if (is_file(public_path('hot')) || ! is_file($manifest)) {
            return self::$nilai = 'dev';
        }

        return self::$nilai = substr(md5_file($manifest), 0, 12);
    }
}
