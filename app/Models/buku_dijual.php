<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class buku_dijual extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'buku_dijual';

    protected $guarded = ['id'];

    // has many testimoni_pembeli
    public function testimoni_pembeli()
    {
        return $this->hasMany(testimoni_pembeli::class);
    }

    // has many list_transaksi_buku
    public function list_transaksi_buku()
    {
        return $this->hasMany(list_transaksi_buku::class);
    }

    //  has many bukudijual_penulis_pivot
    public function bukudijual_penulis_pivot()
    {
        return $this->hasMany(bukudijual_penulis_pivot::class);
    }

    // has many storage_buku_dijual
    public function storage_buku_dijual()
    {
        return $this->hasMany(storage_buku_dijual::class);
    }

    // has many keranjang 
    public function keranjang()
    {
        return $this->hasMany(keranjang::class);
    }

    // has many buku_lunas_user
    public function buku_lunas_user()
    {
        return $this->hasMany(buku_lunas_user::class);
    }

    // belongs to kategori
    public function kategori()
    {
        return $this->belongsTo(kategori::class, 'kategori_id');
    }
}
