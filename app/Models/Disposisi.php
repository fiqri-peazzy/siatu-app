<?php

namespace App\Models;

use App\Enums\StatusDisposisi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disposisi extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'surat_masuk_id',
        'parent_disposisi_id',
        'level',
        'dari_user_id',
        'ke_user_id',
        'catatan',
        'status',
        'tgl_disposisi',
    ];

    protected $casts = [
        'tgl_disposisi' => 'datetime',
        'status' => StatusDisposisi::class,
    ];

    public function suratMasuk(): BelongsTo
    {
        return $this->belongsTo(SuratMasuk::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Disposisi::class, 'parent_disposisi_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Disposisi::class, 'parent_disposisi_id');
    }

    public function dariUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dari_user_id');
    }

    public function keUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ke_user_id');
    }
}
