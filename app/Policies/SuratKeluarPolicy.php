<?php

namespace App\Policies;

use App\Models\SuratKeluar;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SuratKeluarPolicy
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
    public function view(User $user, SuratKeluar $suratKeluar): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin-tu', 'kepala-sekolah', 'staf-tu']);
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
    public function update(User $user, SuratKeluar $suratKeluar): bool
    {
        // Hanya bisa diedit jika status masih draft atau revisi
        if (!in_array($suratKeluar->status->value, ['draft', 'revisi'])) {
            return false;
        }

        if ($user->hasAnyRole(['super-admin', 'admin-tu'])) {
            return true;
        }

        // Staf TU hanya bisa edit draftnya sendiri
        if ($user->hasRole('staf-tu')) {
            return $suratKeluar->dibuat_oleh === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SuratKeluar $suratKeluar): bool
    {
        return $user->hasAnyRole(['super-admin', 'admin-tu']);
    }

    /**
     * Determine whether the user can approve/reject the model.
     */
    public function approve(User $user, SuratKeluar $suratKeluar): bool
    {
        return $user->hasAnyRole(['super-admin', 'kepala-sekolah']);
    }
}
