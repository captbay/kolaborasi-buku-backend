<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class buku_kolaborasi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'buku_kolaborasi';

    protected $guarded = ['id'];

    // has many bab_buku_kolaborasi
    public function bab_buku_kolaborasi()
    {
        return $this->hasMany(bab_buku_kolaborasi::class);
    }

    // belongs to kategori
    public function kategori()
    {
        return $this->belongsTo(kategori::class, 'kategori_id');
    }
}
