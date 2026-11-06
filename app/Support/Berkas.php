<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * URL berkas di disk R2 (swafoto absen, foto profil, dokumentasi jurnal, bukti izin, lampiran surat).
 *
 * Mode (lihat docs/STORAGE_R2.md):
 *  - privat (bawaan): signed URL R2 yang kedaluwarsa — bucket tidak perlu dibuka publik,
 *    tidak butuh subdomain/DNS. URL di-cache 50 menit agar stabil & bisa di-cache browser.
 *  - publik: CLOUDFLARE_R2_PUBLIK=true + CLOUDFLARE_R2_URL=https://storage.<domain> (custom domain R2).
 *  - lokal (CLOUDFLARE_R2_LOCAL=true): URL disk lokal untuk development.
 */
class Berkas
{
    private const BERLAKU_MENIT = 120;

    private const CACHE_MENIT = 50;

    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }
        $disk = Storage::disk('r2');
        $konfig = config('filesystems.disks.r2');

        if (($konfig['driver'] ?? null) !== 's3') {
            return $disk->url($path);
        }
        if (($konfig['publik'] ?? false) && filled($konfig['url'] ?? null)) {
            return rtrim($konfig['url'], '/').'/'.ltrim($path, '/');
        }

        try {
            return Cache::remember('berkas:'.sha1($path), now()->addMinutes(self::CACHE_MENIT),
                fn () => $disk->temporaryUrl($path, now()->addMinutes(self::BERLAKU_MENIT)));
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }

    /** Nama file acak yang tidak bisa ditebak (pengganti uniqid() yang berbasis waktu) */
    public static function namaAcak(string $ekstensi): string
    {
        return Str::lower((string) Str::ulid()).Str::random(16).'.'.ltrim(strtolower($ekstensi), '.');
    }
}
