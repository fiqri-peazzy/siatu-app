<?php

namespace App\Models;

use App\Enums\AksiPengecualianJadwal;
use App\Models\Master\MasterSlotJam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JadwalPengecualian extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tanggal',
        'slot_jam_id',
        'aksi',
        'label',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'aksi' => AksiPengecualianJadwal::class,
    ];

    public function slot(): BelongsTo
    {
        return $this->belongsTo(MasterSlotJam::class, 'slot_jam_id');
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
