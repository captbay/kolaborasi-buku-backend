<?php

namespace App\Observers;

use App\Models\konten_event;
use Illuminate\Support\Facades\Storage;

class KontenEventObserver
{
    /**
     * Handle the konten_event "created" event.
     */
    public function created(konten_event $konten_event): void
    {
        $originalCover = $konten_event->getOriginal('file');

        if ($konten_event->isDirty('file') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the konten_event "updated" event.
     */
    public function updated(konten_event $konten_event): void
    {
        $originalCover = $konten_event->getOriginal('file');

        if ($konten_event->isDirty('file') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the konten_event "deleted" event.
     */
    public function deleted(konten_event $konten_event): void
    {
        if (!is_null($konten_event->file)) {
            Storage::disk('public')->delete($konten_event->file);
        }
    }

    /**
     * Handle the konten_event "restored" event.
     */
    public function restored(konten_event $konten_event): void
    {
        //
    }

    /**
     * Handle the konten_event "force deleted" event.
     */
    public function forceDeleted(konten_event $konten_event): void
    {
        if (!is_null($konten_event->file)) {
            Storage::disk('public')->delete($konten_event->file);
        }
    }

    public function saved(konten_event $konten_event): void
    {
        $originalCover = $konten_event->getOriginal('file');

        if ($konten_event->isDirty('file') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }
}
