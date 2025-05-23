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
        $originalCover = $buku_dijual->getOriginal('cover_buku');

        if ($buku_dijual->isDirty('cover_buku') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $buku_dijual->getOriginal('file_buku');

        if ($buku_dijual->isDirty('file_buku') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the buku_dijual "updated" event.
     */
    public function updated(buku_dijual $buku_dijual): void
    {
        $originalCover = $buku_dijual->getOriginal('cover_buku');

        if ($buku_dijual->isDirty('cover_buku') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $buku_dijual->getOriginal('file_buku');

        if ($buku_dijual->isDirty('file_buku') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the buku_dijual "deleted" event.
     */
    public function deleted(buku_dijual $buku_dijual): void
    {
        if (!is_null($buku_dijual->storage_buku_dijual()->get('nama_generate'))) {
            // get all the name_generate
            $name_generate = $buku_dijual->storage_buku_dijual()->get('nama_generate');
            // foreach name_generate
            foreach ($name_generate as $name) {
                // delete the file
                Storage::disk('public')->delete($name['nama_generate']);
            }
        }

        if (!is_null($buku_dijual->cover_buku)) {
            Storage::disk('public')->delete($buku_dijual->cover_buku);
        }

        if (!is_null($buku_dijual->file_buku)) {
            Storage::disk('public')->delete($buku_dijual->file_buku);
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
        if (!is_null($buku_dijual->storage_buku_dijual()->get('nama_generate'))) {
            // get all the name_generate
            $name_generate = $buku_dijual->storage_buku_dijual()->get('nama_generate');
            // foreach name_generate
            foreach ($name_generate as $name) {
                // delete the file
                Storage::disk('public')->delete($name['nama_generate']);
            }
        }

        if (!is_null($buku_dijual->cover_buku)) {
            Storage::disk('public')->delete($buku_dijual->cover_buku);
        }

        if (!is_null($buku_dijual->file_buku)) {
            Storage::disk('public')->delete($buku_dijual->file_buku);
        }
    }

    public function saved(buku_dijual $buku_dijual): void
    {
        $originalCover = $buku_dijual->getOriginal('cover_buku');

        if ($buku_dijual->isDirty('cover_buku') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $buku_dijual->getOriginal('file_buku');

        if ($buku_dijual->isDirty('file_buku') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }
}
