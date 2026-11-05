<?php

namespace App\Models\Master;

use App\Enums\Hari;
use App\Enums\JenisSlotJam;
use App\Models\JadwalSesi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterSlotJam extends Model
{
    protected $fillable = [
        'pola_jam_id',
        'hari',
        'urutan',
        'jam_ke',
        'jam_mulai',
        'jam_selesai',
        'jenis',
        'label',
    ];

    protected $casts = [
        'hari' => Hari::class,
        'jenis' => JenisSlotJam::class,
        'urutan' => 'integer',
        'jam_ke' => 'integer',
    ];

    public function polaJam(): BelongsTo
    {
        return $this->belongsTo(MasterPolaJam::class, 'pola_jam_id');
    }

    public function sesis(): HasMany
    {
        return $this->hasMany(JadwalSesi::class, 'slot_jam_id');
    }

    /** "07:15–07:45" */
    public function rentang(): string
    {
        return substr($this->jam_mulai, 0, 5).'–'.substr($this->jam_selesai, 0, 5);
    }
}
