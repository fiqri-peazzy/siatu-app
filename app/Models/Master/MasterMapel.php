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
}
