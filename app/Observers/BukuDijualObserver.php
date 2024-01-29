<?php

namespace App\Observers;

use App\Models\buku_dijual;
use Illuminate\Support\Facades\Storage;

class BukuDijualObserver
{
    /**
     * Handle the buku_dijual "created" event.
     */
    public function created(buku_dijual $buku_dijual): void
    {
        if ($buku_dijual->isDirty('cover_buku')) {
            Storage::disk('public')->delete($buku_dijual->getOriginal('cover_buku'));
        }
    }

    /**
     * Handle the buku_dijual "updated" event.
     */
    public function updated(buku_dijual $buku_dijual): void
    {
        // delete the old cover_buku from storage
        if ($buku_dijual->isDirty('cover_buku')) {
            Storage::disk('public')->delete($buku_dijual->getOriginal('cover_buku'));
        }
    }

    /**
     * Handle the buku_dijual "deleted" event.
     */
    public function deleted(buku_dijual $buku_dijual): void
    {
        if (!is_null($buku_dijual->cover_buku)) {
            Storage::disk('public')->delete($buku_dijual->cover_buku);
        }
    }

    /**
     * Handle the buku_dijual "restored" event.
     */
    public function restored(buku_dijual $buku_dijual): void
    {
        //
    }

    /**
     * Handle the buku_dijual "force deleted" event.
     */
    public function forceDeleted(buku_dijual $buku_dijual): void
    {
        if (!is_null($buku_dijual->cover_buku)) {
            Storage::disk('public')->delete($buku_dijual->cover_buku);
        }
    }

    public function saved(buku_dijual $buku_dijual): void
    {
        if ($buku_dijual->isDirty('cover_buku')) {
            Storage::disk('public')->delete($buku_dijual->getOriginal('cover_buku'));
        }
    }
}
