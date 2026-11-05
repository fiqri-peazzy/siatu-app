<?php

namespace App\Models;

use App\Enums\JenisSesi;
use App\Models\Master\MasterGuru;
use App\Models\Master\MasterKelas;
use App\Models\Master\MasterMapel;
use App\Models\Master\MasterSlotJam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Isi satu slot jam untuk satu/lebih rombel (lihat migration jadwal_sesis).
 */
class JadwalSesi extends Model
{
    protected $fillable = [
        'slot_jam_id',
        'jenis',
        'mapel_id',
        'guru_id',
        'guru_manual',
        'label',
        'grup_pilihan',
    ];

    protected $casts = [
        'jenis' => JenisSesi::class,
        'guru_manual' => 'boolean',
    ];

    public function slot(): BelongsTo
    {
        return $this->belongsTo(MasterSlotJam::class, 'slot_jam_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(MasterMapel::class, 'mapel_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(MasterGuru::class, 'guru_id');
    }

    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(MasterKelas::class, 'jadwal_sesi_kelas', 'jadwal_sesi_id', 'kelas_id');
    }

    /** Sesi milik pola jam tertentu */
    public function scopeUntukPola(Builder $query, int $polaJamId): Builder
    {
        return $query->whereHas('slot', fn ($q) => $q->where('pola_jam_id', $polaJamId));
    }

    public function namaTampil(): string
    {
        return $this->mapel?->nama ?? $this->label ?? '-';
    }
}
