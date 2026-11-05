<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterPolaJam extends Model
{
    protected $fillable = [
        'tahun_ajaran_id',
        'nama',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(MasterTahunAjaran::class, 'tahun_ajaran_id');
    }

    public function slots(): HasMany
    {
        return $this->hasMany(MasterSlotJam::class, 'pola_jam_id')->orderBy('urutan');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
