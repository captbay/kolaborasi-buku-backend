<?php

namespace App\Filament\Resources\BukuDijualResource\Pages;

use App\Filament\Resources\BukuDijualResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;


class CreateBukuDijual extends CreateRecord
{
    protected static string $resource = BukuDijualResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['judul']);
        $data['penerbit'] = env('APP_NAME');

        return $data;
    }

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
