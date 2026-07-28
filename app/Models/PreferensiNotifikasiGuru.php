<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreferensiNotifikasiGuru extends Model
{
    protected $fillable = [
        'guru_id',
        'reminder_jurnal_aktif',
        'kanal_notifikasi',
    ];

    public function guru()
    {
        return $this->belongsTo(Master\MasterGuru::class, 'guru_id');
    }
}
