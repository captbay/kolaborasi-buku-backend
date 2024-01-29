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
        //
        if ($user->isDirty('foto_profil')) {
            Storage::disk('public')->delete($user->getOriginal('foto_profil'));
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
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
    }

    public function saved(User $user): void
    {
        if ($user->isDirty('foto_profil')) {
            Storage::disk('public')->delete($user->getOriginal('foto_profil'));
        }
    }
}
