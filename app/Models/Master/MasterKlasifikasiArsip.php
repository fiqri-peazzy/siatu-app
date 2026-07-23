<?php

namespace App\Models\Master;

use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterKlasifikasiArsip extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
    ];

    public function suratMasuks(): HasMany
    {
        return $this->hasMany(SuratMasuk::class, 'klasifikasi_arsip_id');
    }

    public function suratKeluars(): HasMany
    {
        return $this->hasMany(SuratKeluar::class, 'klasifikasi_arsip_id');
    }
}
