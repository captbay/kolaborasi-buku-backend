<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaksi_paket_penerbitan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'transaksi_paket_penerbitan';

    protected $guarded = ['id'];

    // belongs to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // belongs to paket_penerbitan
    public function paket_penerbitan()
    {
        return $this->belongsTo(paket_penerbitan::class, 'paket_penerbitan_id');
    }

    // belongs to buku_permohonan_terbit
    public function buku_permohonan_terbit()
    {
        return $this->belongsTo(buku_permohonan_terbit::class, 'buku_permohonan_terbit_id');
    }

    // has many jasa_transaksi_paket_penerbitan
    public function trx_jasa_penerbitan()
    {
        return $this->hasMany(trx_jasa_penerbitan::class, 'transaksi_paket_penerbitan_id');
    }

    public function jasa_tambahan()
    {
        return $this->belongsToMany(jasa_tambahan::class, 'trx_jasa_penerbitan', 'transaksi_paket_penerbitan_id', 'jasa_tambahan_id');
    }
}
