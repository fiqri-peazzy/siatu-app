<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;


class MasterMapel extends Model
{


    protected $fillable = [
        'kode',
        'nama',
        'kelompok',
        'jam',
        'kurikulum',
    ];

    public function peminatanSiswas()
    {
        return $this->belongsToMany(MasterSiswa::class, 'peminatan_siswas', 'mapel_id', 'siswa_id')
                    ->withPivot('tahun_ajaran_id')
                    ->withTimestamps();
    }
}
