<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notifikasi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'notifikasi';

    protected $guarded = ['id'];

    // belongs to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
