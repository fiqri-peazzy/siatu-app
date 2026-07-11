<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiGuru extends Model
{
    protected $guarded = [];

    public function guru()
    {
        return $this->belongsTo(Master\MasterGuru::class, 'guru_id');
    }
}
