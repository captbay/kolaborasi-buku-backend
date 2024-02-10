<?php

namespace App\Observers;

use App\Models\config_web;
use Illuminate\Support\Facades\Storage;

class ConfigWebObserver
{
    /**
     * Handle the config_web "created" event.
     */
    public function created(config_web $config_web): void
    {
        $originalCover = $config_web->getOriginal('value');

        if ($config_web->isDirty('value') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the config_web "updated" event.
     */
    public function updated(config_web $config_web): void
    {
        $originalCover = $config_web->getOriginal('value');

        if ($config_web->isDirty('value') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }

    /**
     * Handle the config_web "deleted" event.
     */
    public function deleted(config_web $config_web): void
    {
        if (!is_null($config_web->value)) {
            Storage::disk('public')->delete($config_web->value);
        }
    }

    /**
     * Handle the config_web "restored" event.
     */
    public function restored(config_web $config_web): void
    {
        //
    }

    /**
     * Handle the config_web "force deleted" event.
     */
    public function forceDeleted(config_web $config_web): void
    {
        if (!is_null($config_web->value)) {
            Storage::disk('public')->delete($config_web->value);
        }
    }

    public function saved(config_web $config_web): void
    {
        $originalCover = $config_web->getOriginal('value');

        if ($config_web->isDirty('value') && $originalCover !== null) {
            Storage::disk('public')->delete($originalCover);
        }
    }
}
