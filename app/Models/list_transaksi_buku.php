<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class list_transaksi_buku extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'list_transaksi_buku';

    protected $guarded = ['id'];

    // belongs to transaksi_penjualan_buku
    public function transaksi_penjualan_buku()
    {
        return $this->belongsTo(transaksi_penjualan_buku::class, 'transaksi_penjualan_buku_id');
    }

    // belongs to buku_dijual
    public function buku_dijual()
    {
        return $this->belongsTo(buku_dijual::class, 'buku_dijual_id');
    }
}
