<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterMapel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode',
        'nama',
        'jam',
        'kurikulum',
    ];
}
