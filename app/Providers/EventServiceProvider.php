<?php

namespace App\Providers;

use App\Models\buku_dijual;
use App\Models\buku_kolaborasi;
use App\Models\buku_permohonan_terbit;
use App\Models\storage_buku_dijual;
use App\Models\User;
use App\Models\user_bab_buku_kolaborasi;
use App\Observers\BukuDijualObserver;
use App\Observers\BukuKolaborasiObserver;
use App\Observers\BukuPermohonanTerbitObserver;
use App\Observers\StorageBukuDijualObserver;
use App\Observers\UserBabBukuKolaborasiObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        buku_dijual::observe(BukuDijualObserver::class);
        buku_kolaborasi::observe(BukuKolaborasiObserver::class);
        user_bab_buku_kolaborasi::observe(UserBabBukuKolaborasiObserver::class);
        storage_buku_dijual::observe(StorageBukuDijualObserver::class);
        buku_permohonan_terbit::observe(BukuPermohonanTerbitObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
