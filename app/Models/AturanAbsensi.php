<?php

namespace App\Models;

use App\Enums\Hari;
use App\Enums\KategoriAbsensi;
use Illuminate\Database\Eloquent\Model;

class AturanAbsensi extends Model
{
    protected $fillable = [
        'kategori',
        'hari',
        'hari_kerja',
        'masuk_buka',
        'masuk_tepat',
        'toleransi_menit',
        'masuk_tutup',
        'pulang_buka',
        'pulang_tutup',
        'pulang_cepat_diizinkan',
    ];

    protected $casts = [
        'kategori' => KategoriAbsensi::class,
        'hari' => Hari::class,
        'hari_kerja' => 'boolean',
        'toleransi_menit' => 'integer',
        'pulang_cepat_diizinkan' => 'boolean',
    ];

    /** Jam dalam format "HH:MM" */
    public function jam(string $kolom): string
    {
        return substr((string) $this->{$kolom}, 0, 5);
    }
}
