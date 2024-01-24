<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class konten_event extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'konten_event';

    protected $guarded = ['id'];
}
