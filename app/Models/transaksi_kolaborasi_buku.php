<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaksi_kolaborasi_buku extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'transaksi_kolaborasi_buku';

    protected $guarded = ['id'];

    // belongs to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // belongs to bab_buku_kolaborasi
    public function bab_buku_kolaborasi()
    {
        return $this->belongsTo(bab_buku_kolaborasi::class, 'bab_buku_kolaborasi_id');
    }
}
