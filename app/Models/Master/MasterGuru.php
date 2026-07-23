<?php

namespace App\Models\Master;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterGuru extends Model
{

    protected $fillable = [
        'nip',
        'nuptk',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'no_hp',
        'email',
        'alamat',
        'jabatan',
        'mapel_id',
        'pendidikan_terakhir',
        'status_kepegawaian',
        'tmt_tugas',
        'foto_path',
        'status',
        'user_id',
        'status_akun',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tmt_tugas' => 'date',
        'status_kepegawaian' => \App\Enums\StatusKepegawaianGuru::class,
        'status_akun' => \App\Enums\StatusAkun::class,
        'status' => \App\Enums\StatusPersonil::class,
    ];

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(MasterMapel::class, 'mapel_id');
    }

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

    protected static function booted()
    {
        static::deleting(function ($guru) {
            if ($guru->user) {
                if ($guru->isForceDeleting()) {
                    $guru->user()->forceDelete();
                } else {
                    $guru->user()->delete();
                }
            }
        });
    }
}
