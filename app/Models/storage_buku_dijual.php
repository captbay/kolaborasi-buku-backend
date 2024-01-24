<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class storage_buku_dijual extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'storage_buku_dijual';

    protected $guarded = ['id'];

    // belongs to buku_dijual
    public function buku_dijual()
    {
        return $this->belongsTo(buku_dijual::class, 'buku_dijual_id');
    }
}
