<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kategori extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kategori';

    protected $guarded = ['id'];

    // has many buku_dijual
    public function buku_dijual()
    {
        return $this->hasMany(buku_dijual::class);
    }

    // has many buku_kolaborasi
    public function buku_kolaborasi()
    {
        return $this->hasMany(buku_kolaborasi::class);
    }
}
