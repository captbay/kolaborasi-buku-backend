<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class buku_permohonan_terbit extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'buku_permohonan_terbit';

    protected $guarded = ['id'];

    // has many transaksi_paket_penerbitan
    public function transaksi_paket_penerbitan()
    {
        return $this->hasOne(transaksi_paket_penerbitan::class);
    }

    // belongs to user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
