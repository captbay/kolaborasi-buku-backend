<?php

namespace App\Observers;

use App\Models\mou;
use Illuminate\Support\Facades\Storage;

class MouObserver
{
    /**
     * Handle the mou "created" event.
     */
    public function created(mou $mou): void
    {
        $originalCover = $mou->getOriginal('file_mou');

        if ($mou->isDirty('file_mou') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the mou "updated" event.
     */
    public function updated(mou $mou): void
    {
        $originalCover = $mou->getOriginal('file_mou');

        if ($mou->isDirty('file_mou') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the mou "deleted" event.
     */
    public function deleted(mou $mou): void
    {
        if (!is_null($mou->file_mou)) {
            Storage::disk('public')->delete($mou->file_mou);
        }
    }

    /**
     * Handle the mou "restored" event.
     */
    public function restored(mou $mou): void
    {
        //
    }

    /**
     * Handle the mou "force deleted" event.
     */
    public function forceDeleted(mou $mou): void
    {
        $originalCover = $mou->getOriginal('file_mou');

        if ($mou->isDirty('file_mou') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    public function saved(mou $mou): void
    {
        $originalCover = $mou->getOriginal('file_mou');

        if ($mou->isDirty('file_mou') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }
}
