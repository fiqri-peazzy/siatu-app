<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class MasterJadwalPelajaran extends Model
{
    protected $fillable = [
        'tahun_ajaran_id',
        'kelas_id',
        'mapel_id',
        'guru_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'jam_ke',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(MasterTahunAjaran::class, 'tahun_ajaran_id');
    }

    public function kelas()
    {
        return $this->belongsTo(MasterKelas::class, 'kelas_id');
    }

    public function mapel()
    {
        return $this->belongsTo(MasterMapel::class, 'mapel_id');
    }

    public function guru()
    {
        return $this->belongsTo(MasterGuru::class, 'guru_id');
    }
}
