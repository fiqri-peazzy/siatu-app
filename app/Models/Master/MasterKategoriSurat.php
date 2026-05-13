<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterKategoriSurat extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode',
        'nama',
        'format_nomor',
        'counter',
    ];
}
