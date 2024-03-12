<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class jasa_paket_penerbitan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'jasa_paket_penerbitan';

    protected $guarded = ['id'];

    // belongs to paket_penerbitan
    public function paket_penerbitan()
    {
        return $this->belongsTo(paket_penerbitan::class, 'paket_id');
    }
}
