<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class mou extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'mou';

    protected $guarded = ['id'];
}
