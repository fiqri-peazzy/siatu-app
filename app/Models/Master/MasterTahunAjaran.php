<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterTahunAjaran extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama',
        'semester',
        'mulai',
        'selesai',
        'is_active',
    ];

    protected $casts = [
        'mulai' => 'date',
        'selesai' => 'date',
        'is_active' => 'boolean',
    ];

    public function kelas(): HasMany
    {
        return $this->hasMany(MasterKelas::class, 'tahun_ajaran_id');
    }
}
