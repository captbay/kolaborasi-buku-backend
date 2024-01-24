<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paket_penerbitan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'paket_penerbitan';

    protected $guarded = ['id'];

    // has many transaksi_paket_penerbitan
    public function transaksi_paket_penerbitan()
    {
        return $this->hasMany(transaksi_paket_penerbitan::class);
    }
}
