<?php

namespace App\Services;

use App\Models\Pengaturan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Pengaturan sistem yang dapat diubah admin (tabel pengaturans), dengan cache.
 *
 * Identitas sekolah disimpan dengan prefix "sekolah." dan menimpa config('sekolah')
 * saat aplikasi boot, sehingga kop surat/laporan ikut berubah tanpa mengedit .env.
 */
class PengaturanService
{
    private const CACHE = 'pengaturan.semua';

    /** Kolom identitas yang boleh diubah dari UI: key config => label */
    public const IDENTITAS = [
        'nama' => 'Nama sekolah',
        'npsn' => 'NPSN',
        'akreditasi' => 'Akreditasi',
        'alamat' => 'Alamat',
        'kota' => 'Kota/Kabupaten',
        'provinsi' => 'Provinsi',
        'telepon' => 'Telepon',
        'email' => 'Email',
        'kepala' => 'Nama kepala sekolah',
        'nip_kepala' => 'NIP/NBM kepala sekolah',
        'kepala_tu' => 'Nama kepala tata usaha',
        'nip_ka_tu' => 'NIP/NBM kepala tata usaha',
    ];

    /** Nilai bawaan pengaturan notifikasi */
    public const NOTIFIKASI = [
        'reminder_jurnal_aktif' => '1',
        'reminder_jurnal_jam' => '14:00',
        'ambang_alfa_siswa' => '3',
    ];

    /** @return array<string, string|null> */
    public function semua(): array
    {
        try {
            return Cache::rememberForever(self::CACHE, fn () => Schema::hasTable('pengaturans')
                ? Pengaturan::pluck('value', 'key')->all()
                : []);
        } catch (Throwable) {
            return []; // DB belum siap (instalasi awal / build)
        }
    }

    public function nilai(string $key, mixed $default = null): mixed
    {
        $v = $this->semua()[$key] ?? null;

        return ($v === null || $v === '') ? (self::NOTIFIKASI[$key] ?? $default) : $v;
    }

    public function aktif(string $key): bool
    {
        return filter_var($this->nilai($key), FILTER_VALIDATE_BOOLEAN);
    }

    /** @param array<string, mixed> $data key => value */
    public function simpan(array $data, ?string $tipe = null): void
    {
        DB::transaction(function () use ($data, $tipe) {
            foreach ($data as $key => $value) {
                Pengaturan::updateOrCreate(['key' => $key], array_filter([
                    'value' => is_bool($value) ? ($value ? '1' : '0') : (string) ($value ?? ''),
                    'type' => $tipe,
                ], fn ($v) => $v !== null));
            }
        });
        Cache::forget(self::CACHE);
        $this->terapkanIdentitas();
    }

    /** Timpa config('sekolah.*') dengan nilai yang diisi admin (yang kosong tetap pakai .env/default). */
    public function terapkanIdentitas(): void
    {
        foreach (array_keys(self::IDENTITAS) as $k) {
            $v = $this->semua()["sekolah.{$k}"] ?? null;
            if ($v !== null && $v !== '') {
                config(["sekolah.{$k}" => $v]);
            }
        }
    }
}
