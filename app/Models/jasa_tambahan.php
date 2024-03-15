<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class jasa_tambahan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'jasa_tambahan';

    protected $guarded = ['id'];

    public function trx_jasa_penerbitan()
    {
        return $this->hasMany(trx_jasa_penerbitan::class, 'jasa_tambahan_id');
    }
}
