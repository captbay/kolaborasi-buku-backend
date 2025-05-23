<?php

namespace App\Observers;

use App\Models\buku_permohonan_terbit;
use Illuminate\Support\Facades\Storage;

class BukuPermohonanTerbitObserver
{
    /**
     * Handle the buku_permohonan_terbit "created" event.
     */
    public function created(buku_permohonan_terbit $buku_permohonan_terbit): void
    {
        $originalCover = $buku_permohonan_terbit->getOriginal('cover_buku');

        if ($buku_permohonan_terbit->isDirty('cover_buku') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $buku_permohonan_terbit->getOriginal('file_buku');

        if ($buku_permohonan_terbit->isDirty('file_buku') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $file_mou = $buku_permohonan_terbit->getOriginal('file_mou');

        if ($buku_permohonan_terbit->isDirty('file_mou') && $file_mou !== null) {
            Storage::disk('public')->delete($file_mou);
        }
    }

    /**
     * Handle the buku_permohonan_terbit "updated" event.
     */
    public function updated(buku_permohonan_terbit $buku_permohonan_terbit): void
    {
        $originalCover = $buku_permohonan_terbit->getOriginal('cover_buku');

        if ($buku_permohonan_terbit->isDirty('cover_buku') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $buku_permohonan_terbit->getOriginal('file_buku');

        if ($buku_permohonan_terbit->isDirty('file_buku') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $file_mou = $buku_permohonan_terbit->getOriginal('file_mou');

        if ($buku_permohonan_terbit->isDirty('file_mou') && $file_mou !== null) {
            Storage::disk('public')->delete($file_mou);
        }
    }

    /**
     * Handle the buku_permohonan_terbit "deleted" event.
     */
    public function deleted(buku_permohonan_terbit $buku_permohonan_terbit): void
    {
        if (!is_null($buku_permohonan_terbit->cover_buku)) {
            Storage::disk('public')->delete($buku_permohonan_terbit->cover_buku);
        }

        if (!is_null($buku_permohonan_terbit->file_buku)) {
            Storage::disk('public')->delete($buku_permohonan_terbit->file_buku);
        }

        if (!is_null($buku_permohonan_terbit->file_mou)) {
            Storage::disk('public')->delete($buku_permohonan_terbit->file_mou);
        }
    }

    /**
     * Handle the buku_permohonan_terbit "restored" event.
     */
    public function restored(buku_permohonan_terbit $buku_permohonan_terbit): void
    {
        //
    }

    /**
     * Handle the buku_permohonan_terbit "force deleted" event.
     */
    public function forceDeleted(buku_permohonan_terbit $buku_permohonan_terbit): void
    {
        if (!is_null($buku_permohonan_terbit->cover_buku)) {
            Storage::disk('public')->delete($buku_permohonan_terbit->cover_buku);
        }

        if (!is_null($buku_permohonan_terbit->file_buku)) {
            Storage::disk('public')->delete($buku_permohonan_terbit->file_buku);
        }

        if (!is_null($buku_permohonan_terbit->file_mou)) {
            Storage::disk('public')->delete($buku_permohonan_terbit->file_mou);
        }
    }



    public function saved(buku_permohonan_terbit $buku_permohonan_terbit): void
    {
        $originalCover = $buku_permohonan_terbit->getOriginal('cover_buku');

        if ($buku_permohonan_terbit->isDirty('cover_buku') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $originalCover = $buku_permohonan_terbit->getOriginal('file_buku');

        if ($buku_permohonan_terbit->isDirty('file_buku') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }

        $file_mou = $buku_permohonan_terbit->getOriginal('file_mou');

        if ($buku_permohonan_terbit->isDirty('file_mou') && $file_mou !== null) {
            Storage::disk('public')->delete($file_mou);
        }
    }
}
