<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class hubungi_kami extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'hubungi_kami';

    protected $guarded = ['id'];
}
