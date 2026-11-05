<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pembagian tugas mengajar: guru untuk satu mapel di satu rombel per tahun ajaran.
 */
class MasterPengampu extends Model
{
    protected $fillable = [
        'tahun_ajaran_id',
        'kelas_id',
        'mapel_id',
        'guru_id',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(MasterTahunAjaran::class, 'tahun_ajaran_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(MasterKelas::class, 'kelas_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(MasterMapel::class, 'mapel_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(MasterGuru::class, 'guru_id');
    }
}
