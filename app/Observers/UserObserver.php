<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $originalCover = $user->getOriginal('foto_profil');

        if ($user->isDirty('foto_profil') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $user->getOriginal('file_cv');

        if ($user->isDirty('file_cv') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $user->getOriginal('file_ktp');

        if ($user->isDirty('file_ktp') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $user->getOriginal('file_ttd');

        if ($user->isDirty('file_ttd') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $originalCover = $user->getOriginal('foto_profil');

        if ($user->isDirty('foto_profil') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $user->getOriginal('file_cv');

        if ($user->isDirty('file_cv') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $user->getOriginal('file_ktp');

        if ($user->isDirty('file_ktp') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $user->getOriginal('file_ttd');

        if ($user->isDirty('file_ttd') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        if (!is_null($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }
        if (!is_null($user->file_cv)) {
            Storage::disk('public')->delete($user->file_cv);
        }
        if (!is_null($user->file_ktp)) {
            Storage::disk('public')->delete($user->file_ktp);
        }
        if (!is_null($user->file_ttd)) {
            Storage::disk('public')->delete($user->file_ttd);
        }
    }

    public function saved(User $user): void
    {
        $originalCover = $user->getOriginal('foto_profil');

        if ($user->isDirty('foto_profil') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $user->getOriginal('file_cv');

        if ($user->isDirty('file_cv') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $user->getOriginal('file_ktp');

        if ($user->isDirty('file_ktp') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $user->getOriginal('file_ttd');

        if ($user->isDirty('file_ttd') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }
}
