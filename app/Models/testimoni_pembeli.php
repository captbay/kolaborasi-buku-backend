<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class testimoni_pembeli extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'testimoni_pembeli';

    protected $guarded = ['id'];

    // belongs to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // belongs to buku_dijual
    public function buku_dijual()
    {
        return $this->belongsTo(buku_dijual::class, 'buku_dijual_id');
    }
}