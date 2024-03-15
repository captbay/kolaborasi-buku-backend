<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class trx_jasa_penerbitan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'trx_jasa_penerbitan';

    protected $guarded = ['id'];

    public function jasa_tambahan()
    {
        return $this->belongsTo(jasa_tambahan::class, 'jasa_tambahan_id');
    }

    public function transaksi_paket_penerbitan()
    {
        return $this->belongsTo(transaksi_paket_penerbitan::class, 'transaksi_paket_penerbitan_id');
    }
}
