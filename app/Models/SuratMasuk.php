<?php

namespace App\Models;

use App\Enums\SifatSurat;
use App\Enums\StatusSuratMasuk;
use App\Models\Master\MasterKategoriSurat;
use App\Models\Master\MasterKlasifikasiArsip;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuratMasuk extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'no_agenda',
        'kategori_surat_id',
        'klasifikasi_arsip_id',
        'tanggal_terima',
        'tanggal_surat',
        'asal_instansi',
        'perihal',
        'sifat',
        'file_path',
        'status',
        'input_oleh',
    ];

    protected $casts = [
        'tanggal_terima' => 'date',
        'tanggal_surat' => 'date',
        'sifat' => SifatSurat::class,
        'status' => StatusSuratMasuk::class,
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(MasterKategoriSurat::class, 'kategori_surat_id');
    }

    public function klasifikasiArsip(): BelongsTo
    {
        return $this->belongsTo(MasterKlasifikasiArsip::class, 'klasifikasi_arsip_id');
    }

    public function inputOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_oleh');
    }

    public function disposisis(): HasMany
    {
        return $this->hasMany(Disposisi::class);
    }
}
