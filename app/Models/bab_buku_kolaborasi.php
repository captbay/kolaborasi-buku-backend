<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bab_buku_kolaborasi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'bab_buku_kolaborasi';

    protected $guarded = ['id'];

    // hasmany transaksi_kolaborasi_buku
    public function transaksi_kolaborasi_buku()
    {
        return $this->hasMany(transaksi_kolaborasi_buku::class);
    }

    //  has many user_bab_buku_kolaborasi
    public function user_bab_buku_kolaborasi()
    {
        return $this->hasMany(user_bab_buku_kolaborasi::class);
    }

    // belongs to buku_kolaborasi
    public function buku_kolaborasi()
    {
        return $this->belongsTo(buku_kolaborasi::class, 'buku_kolaborasi_id');
    }
}
