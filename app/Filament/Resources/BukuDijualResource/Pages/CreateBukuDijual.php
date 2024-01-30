<?php

namespace App\Filament\Resources\BukuDijualResource\Pages;

use App\Filament\Resources\BukuDijualResource;
use App\Models\bukudijual_penulis_pivot;
use App\Models\penulis;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBukuDijual extends CreateRecord
{
    protected static string $resource = BukuDijualResource::class;

    // protected function handleRecordCreation(array $data): Model
    // {
    //     // insert the bukudijual
    //     $record = static::getModel()::create($data);

    //     // create a new pivot bukudijual_penulis_pivot
    //     bukudijual_penulis_pivot::create([
    //         'buku_dijual_id' => $record->id,
    //         'penulis_id' => $data['penulis_id'],
    //     ]);

    //     return $record;
    // }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
