<?php

namespace App\Observers;

use App\Models\storage_buku_dijual;
use Illuminate\Support\Facades\Storage;


class StorageBukuDijualObserver
{
    /**
     * Handle the storage_buku_dijual "created" event.
     */
    public function created(storage_buku_dijual $storage_buku_dijual): void
    {
        $originalCover = $storage_buku_dijual->getOriginal('nama_generate');

        if ($storage_buku_dijual->isDirty('nama_generate') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the storage_buku_dijual "updated" event.
     */
    public function updated(storage_buku_dijual $storage_buku_dijual): void
    {
        $originalCover = $storage_buku_dijual->getOriginal('nama_generate');

        if ($storage_buku_dijual->isDirty('nama_generate') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the storage_buku_dijual "deleted" event.
     */
    public function deleted(storage_buku_dijual $storage_buku_dijual): void
    {
        if (!is_null($storage_buku_dijual->nama_generate)) {
            Storage::disk('public')->delete($storage_buku_dijual->nama_generate);
        }
    }

    /**
     * Handle the storage_buku_dijual "restored" event.
     */
    public function restored(storage_buku_dijual $storage_buku_dijual): void
    {
        //
    }

    /**
     * Handle the storage_buku_dijual "force deleted" event.
     */
    public function forceDeleted(storage_buku_dijual $storage_buku_dijual): void
    {
        if (!is_null($storage_buku_dijual->nama_generate)) {
            Storage::disk('public')->delete($storage_buku_dijual->nama_generate);
        }
    }

    public function saved(storage_buku_dijual $storage_buku_dijual): void
    {
        $originalCover = $storage_buku_dijual->getOriginal('nama_generate');

        if ($storage_buku_dijual->isDirty('nama_generate') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }
}
