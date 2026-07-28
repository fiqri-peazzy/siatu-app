<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalGuru extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
        'dokumentasi' => 'array',
    ];

    public function kelas()
    {
        return $this->belongsTo(Master\MasterKelas::class, 'kelas_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Master\MasterMapel::class, 'mapel_id');
    }
}
