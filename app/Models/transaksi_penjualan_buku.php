<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaksi_penjualan_buku extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'transaksi_penjualan_buku';

    protected $guarded = ['id'];

    // has many list_transasksi_buku
    public function list_transaksi_buku()
    {
        return $this->hasMany(list_transaksi_buku::class);
    }

    // belongs to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
