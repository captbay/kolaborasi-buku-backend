<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class buku_lunas_user extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'buku_lunas_user';

    protected $guarded = ['id'];

    // belongs to user
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
