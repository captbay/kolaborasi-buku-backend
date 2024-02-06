<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, SoftDeletes;

    protected $table = 'users';

    protected $guarded = ['id'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_depan',
        'nama_belakang',
        'email',
        'password',
        'no_telepon',
        'tgl_lahir',
        'gender',
        'alamat',
        'provinsi',
        'kecamatan',
        'kota',
        'kode_pos',
        'foto_profil',
        'bio',
        'status_verif_email',
        'email_verified_at',
        'file_cv',
        'file_ktp',
        'file_ttd',
        'role',
        'active_flag',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@admin.com') && $this->hasVerifiedEmail() && $this->role === 'ADMIN';
    }

    public function getFilamentName(): string
    {
        return $this->nama_depan . ' ' . $this->nama_belakang;
    }

    // has many notifikasi
    public function notifikasi()
    {
        return $this->hasMany(notifikasi::class);
    }

    // has many keranjang
    public function keranjangs()
    {
        return $this->hasMany(keranjang::class);
    }

    // has many testimoni_pembeli
    public function testimoni_pembeli()
    {
        return $this->hasMany(testimoni_pembeli::class);
    }

    // has many buku_lunas_user
    public function buku_lunas_user()
    {
        return $this->hasMany(buku_lunas_user::class);
    }

    // has many transaksi_penjualan_buku
    public function transaksi_penjualan_buku()
    {
        return $this->hasMany(transaksi_penjualan_buku::class);
    }

    // has many transaksi_kolaborasi_buku
    public function transaksi_kolaborasi_buku()
    {
        return $this->hasMany(transaksi_kolaborasi_buku::class);
    }

    // has many user_bab_buku_kolaborasi
    public function user_bab_buku_kolaborasi()
    {
        return $this->hasMany(user_bab_buku_kolaborasi::class);
    }

    // has many buku_permohonan_terbit
    public function buku_permohonan_terbit()
    {
        return $this->hasMany(buku_permohonan_terbit::class);
    }

    // has many transaksi_paket_penerbitan
    public function transaksi_paket_penerbitan()
    {
        return $this->hasMany(transaksi_paket_penerbitan::class);
    }
}
