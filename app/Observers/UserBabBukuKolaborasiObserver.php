<?php

namespace App\Observers;

use App\Models\user_bab_buku_kolaborasi;
use Illuminate\Support\Facades\Storage;

class UserBabBukuKolaborasiObserver
{
    /**
     * Handle the user_bab_buku_kolaborasi "created" event.
     */
    public function created(user_bab_buku_kolaborasi $user_bab_buku_kolaborasi): void
    {
        $originalCover = $user_bab_buku_kolaborasi->getOriginal('file_bab');

        if ($user_bab_buku_kolaborasi->isDirty('file_bab') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the user_bab_buku_kolaborasi "updated" event.
     */
    public function updated(user_bab_buku_kolaborasi $user_bab_buku_kolaborasi): void
    {
        $originalCover = $user_bab_buku_kolaborasi->getOriginal('file_bab');

        if ($user_bab_buku_kolaborasi->isDirty('file_bab') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the user_bab_buku_kolaborasi "deleted" event.
     */
    public function deleted(user_bab_buku_kolaborasi $user_bab_buku_kolaborasi): void
    {
        if (!is_null($user_bab_buku_kolaborasi->file_bab)) {
            Storage::disk('public')->delete($user_bab_buku_kolaborasi->file_bab);
        }
    }

    /**
     * Handle the user_bab_buku_kolaborasi "restored" event.
     */
    public function restored(user_bab_buku_kolaborasi $user_bab_buku_kolaborasi): void
    {
        //
    }

    /**
     * Handle the user_bab_buku_kolaborasi "force deleted" event.
     */
    public function forceDeleted(user_bab_buku_kolaborasi $user_bab_buku_kolaborasi): void
    {
        if (!is_null($user_bab_buku_kolaborasi->file_bab)) {
            Storage::disk('public')->delete($user_bab_buku_kolaborasi->file_bab);
        }
    }

    /**
     * Handle the user_bab_buku_kolaborasi "saved" event.
     */
    public function saved(user_bab_buku_kolaborasi $user_bab_buku_kolaborasi): void
    {
        $originalCover = $user_bab_buku_kolaborasi->getOriginal('file_bab');

        if ($user_bab_buku_kolaborasi->isDirty('file_bab') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }
}
