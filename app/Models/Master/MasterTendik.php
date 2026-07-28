<?php

namespace App\Models\Master;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterTendik extends Model
{


    protected $table = 'master_tenaga_kependidikans';

    protected $fillable = [
        'nip_nik',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'no_hp',
        'email',
        'alamat',
        'jabatan',
        'unit_kerja',
        'status_kepegawaian',
        'tmt_tugas',
        'foto_path',
        'user_id',
        'status_akun',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tmt_tugas' => 'date',
        'jabatan' => \App\Enums\JabatanTendik::class,
        'status_kepegawaian' => \App\Enums\StatusKepegawaianTendik::class,
        'status_akun' => \App\Enums\StatusAkun::class,
        'status' => \App\Enums\StatusPersonil::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function absensis()
    {
        return $this->hasMany(\App\Models\AbsensiTendik::class, 'tendik_id');
    }
}
