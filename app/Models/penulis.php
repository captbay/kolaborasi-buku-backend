<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class penulis extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'penulis';

    protected $guarded = ['id'];

    // has many bukudijual_penulis_pivot
    public function bukudijual_penulis_pivot()
    {
        return $this->hasMany(bukudijual_penulis_pivot::class, 'penulis_id');
    }

    // belongs to many bukudijual
    public function buku_dijual()
    {
        return $this->belongsToMany(buku_dijual::class, 'bukudijual_penulis_pivot', 'penulis_id', 'buku_dijual_id');
    }
}
