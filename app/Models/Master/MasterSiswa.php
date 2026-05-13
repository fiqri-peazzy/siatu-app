<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterSiswa extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nis',
        'nisn',
        'nama',
        'kelas_id',
        'status',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(MasterKelas::class, 'kelas_id');
    }
}
