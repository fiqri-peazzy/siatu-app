<?php

namespace App\Policies;

use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SuratMasukPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin-tu', 'kepala-sekolah', 'staf-tu']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SuratMasuk $suratMasuk): bool
    {
        if ($user->hasAnyRole(['super-admin', 'admin-tu', 'kepala-sekolah', 'staf-tu'])) {
            return true;
        }

        // Guru bisa view jika ada di rantai disposisi surat ini
        if ($user->hasRole('guru')) {
            return $suratMasuk->disposisis()->where('ke_user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin-tu', 'staf-tu']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SuratMasuk $suratMasuk): bool
    {
        if ($user->hasAnyRole(['super-admin', 'admin-tu'])) {
            return true;
        }

        // Staf TU hanya bisa edit suratnya sendiri
        if ($user->hasRole('staf-tu')) {
            return $suratMasuk->input_oleh === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SuratMasuk $suratMasuk): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin-tu']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SuratMasuk $suratMasuk): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin-tu']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SuratMasuk $suratMasuk): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can dispose the model.
     */
    public function dispose(User $user, SuratMasuk $suratMasuk): bool
    {
        if ($user->hasAnyRole(['super-admin', 'admin-tu', 'kepala-sekolah'])) {
            return true;
        }
        
        // Penerima disposisi sebelumnya berhak meneruskan (disposisi berantai)
        return $suratMasuk->disposisis()->where('ke_user_id', $user->id)->exists();
    }
}
