<?php

namespace App\Models;

use App\Enums\StatusSuratKeluar;
use App\Models\Master\MasterKategoriSurat;
use App\Models\Master\MasterKlasifikasiArsip;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratKeluar extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'no_surat',
        'kategori_surat_id',
        'klasifikasi_arsip_id',
        'surat_masuk_id',
        'tanggal',
        'tujuan',
        'perihal',
        'isi_ringkas',
        'file_draft_path',
        'file_final_path',
        'status',
        'dibuat_oleh',
        'catatan_revisi',
        'disetujui_oleh',
        'disetujui_at',
        'metadata',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'disetujui_at' => 'datetime',
        'status' => StatusSuratKeluar::class,
        'metadata' => 'array',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(MasterKategoriSurat::class, 'kategori_surat_id');
    }

    public function klasifikasiArsip(): BelongsTo
    {
        return $this->belongsTo(MasterKlasifikasiArsip::class, 'klasifikasi_arsip_id');
    }

    public function balasanDari(): BelongsTo
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id');
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
