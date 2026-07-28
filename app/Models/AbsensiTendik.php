<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiTendik extends Model
{
    protected $guarded = [];

    public function tendik()
    {
        return $this->belongsTo(\App\Models\Master\MasterTendik::class, 'tendik_id');
    }
}
