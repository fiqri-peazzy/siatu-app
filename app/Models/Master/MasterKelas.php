<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterKelas extends Model
{


    protected $fillable = [
        'kode',
        'nama',
        'tingkat',
        'jurusan',
        'wali_kelas_id',
        'tahun_ajaran_id',
    ];

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(MasterGuru::class, 'wali_kelas_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(MasterTahunAjaran::class, 'tahun_ajaran_id');
    }

    public function siswa(): HasMany
    {
        return $this->hasMany(MasterSiswa::class, 'kelas_id');
    }

    public function pengampus(): HasMany
    {
        return $this->hasMany(MasterPengampu::class, 'kelas_id');
    }

    public function jadwalSesis(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\JadwalSesi::class, 'jadwal_sesi_kelas', 'kelas_id', 'jadwal_sesi_id');
    }
}
