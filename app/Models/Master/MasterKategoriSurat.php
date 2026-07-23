<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;


class MasterKategoriSurat extends Model
{


    protected $fillable = [
        'kode',
        'nama',
        'tipe',
        'format_nomor',
        'counter',
    ];

    protected $casts = [
        'tipe' => \App\Enums\TipeKategoriSurat::class,
    ];

    public function suratMasuks()
    {
        return $this->hasMany(\App\Models\SuratMasuk::class, 'kategori_surat_id');
    }

    public function suratKeluars()
    {
        return $this->hasMany(\App\Models\SuratKeluar::class, 'kategori_surat_id');
    }
}
