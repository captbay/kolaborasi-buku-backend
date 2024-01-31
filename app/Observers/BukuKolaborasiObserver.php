<?php

namespace App\Observers;

use App\Models\buku_kolaborasi;
use Illuminate\Support\Facades\Storage;

class BukuKolaborasiObserver
{
    /**
     * Handle the buku_kolaborasi "created" event.
     */
    public function created(buku_kolaborasi $buku_kolaborasi): void
    {
        $originalCover = $buku_kolaborasi->getOriginal('cover_buku');

        if ($buku_kolaborasi->isDirty('cover_buku') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $buku_kolaborasi->getOriginal('file_sertifikasi');

        if ($buku_kolaborasi->isDirty('file_sertifikasi') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the buku_kolaborasi "updated" event.
     */
    public function updated(buku_kolaborasi $buku_kolaborasi): void
    {
        $originalCover = $buku_kolaborasi->getOriginal('cover_buku');

        if ($buku_kolaborasi->isDirty('cover_buku') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $buku_kolaborasi->getOriginal('file_sertifikasi');

        if ($buku_kolaborasi->isDirty('file_sertifikasi') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the buku_kolaborasi "deleted" event.
     */
    public function deleted(buku_kolaborasi $buku_kolaborasi): void
    {
        if (!is_null($buku_kolaborasi->cover_buku)) {
            Storage::disk('public')->delete($buku_kolaborasi->cover_buku);
        }
        if (!is_null($buku_kolaborasi->file_sertifikasi)) {
            Storage::disk('public')->delete($buku_kolaborasi->file_sertifikasi);
        }
    }

    /**
     * Handle the buku_kolaborasi "restored" event.
     */
    public function restored(buku_kolaborasi $buku_kolaborasi): void
    {
        //
    }

    /**
     * Handle the buku_kolaborasi "force deleted" event.
     */
    public function forceDeleted(buku_kolaborasi $buku_kolaborasi): void
    {
        if (!is_null($buku_kolaborasi->cover_buku)) {
            Storage::disk('public')->delete($buku_kolaborasi->cover_buku);
        }
        if (!is_null($buku_kolaborasi->file_sertifikasi)) {
            Storage::disk('public')->delete($buku_kolaborasi->file_sertifikasi);
        }
    }

    /**
     * Handle the buku_kolaborasi "updated" event.
     */
    public function saved(buku_kolaborasi $buku_kolaborasi): void
    {
        $originalCover = $buku_kolaborasi->getOriginal('cover_buku');

        if ($buku_kolaborasi->isDirty('cover_buku') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $buku_kolaborasi->getOriginal('file_sertifikasi');

        if ($buku_kolaborasi->isDirty('file_sertifikasi') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }
}
