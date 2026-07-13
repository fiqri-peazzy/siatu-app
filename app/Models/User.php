<?php

namespace App\Models;

use App\Models\Master\MasterGuru;
use App\Models\Master\MasterSiswa;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'nis',
        'no_hp',
        'avatar',
        'jabatan',
        'jenis_kelamin',
        'guru_id',
        'siswa_id',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ─── Relasi ───────────────────────────────────────────────────────────────

    public function guru(): BelongsTo
    {
        return $this->belongsTo(MasterGuru::class, 'guru_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(MasterSiswa::class, 'siswa_id');
    }

    public function masterGuru()
    {
        return $this->hasOne(MasterGuru::class, 'user_id');
    }

    public function masterTendik()
    {
        return $this->hasOne(\App\Models\Master\MasterTendik::class, 'user_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Mendapatkan identifier utama: NIP untuk guru/staf, NIS untuk siswa.
     */
    public function getIdentifierAttribute(): ?string
    {
        return $this->nip ?? $this->nis;
    }

    /**
     * Mendapatkan label role yang ditampilkan dalam bahasa Indonesia.
     */
    public function getRoleLabelAttribute(): string
    {
        $labels = [
            'super-admin'    => 'Super Admin',
            'admin-tu'       => 'Admin TU',
            'kepala-sekolah' => 'Kepala Sekolah',
            'guru'           => 'Guru',
            'staf-tu'        => 'Staf TU',
            'siswa'          => 'Siswa',
        ];

        $role = $this->roles->first()?->name;
        return $labels[$role] ?? 'Pengguna';
    }

    /**
     * Update waktu login terakhir.
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}
