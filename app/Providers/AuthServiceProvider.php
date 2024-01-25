<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return 'http://localhost:8080/reset-password?token=' . $token . '&email=' . $user->email; //pake link di FE, habistu di FE baru pake API buat ganti
        });

        VerifyEmail::toMailUsing(function (User $user, string $url) {
            return (new MailMessage)
                ->greeting('Halo!, ' . $user->nama_depan . ' ' . $user->nama_belakang)
                ->subject('Verifikasi Email Anda')
                ->line('Tekan tombol dibawah untuk melakukan verifikasi email anda.')
                ->action('Verifikasi Sekarang', $url);
        });
    }
}
