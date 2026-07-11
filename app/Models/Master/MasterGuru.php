<?php

namespace App\Models\Master;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterGuru extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'mapel_utama',
        'status',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function waliKelas(): HasMany
    {
        return $this->hasMany(MasterKelas::class, 'wali_kelas_id');
    }


    public function absensis()
    {
        return $this->hasMany(\App\Models\AbsensiGuru::class, 'guru_id');
    }
}
