<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bukudijual_penulis_pivot extends Model
{
    use HasFactory;
    // HasUuids;

    protected $table = 'bukudijual_penulis_pivot';

    // auto create id
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    // belongs to buku_dijual
    public function buku_dijual()
    {
        return $this->belongsTo(buku_dijual::class, 'buku_dijual_id');
    }

    // belongs to penulis
    public function penulis()
    {
        return $this->belongsTo(penulis::class, 'penulis_id');
    }

    // create auto created_at and auto updated_at
    public $timestamps = true;
}
