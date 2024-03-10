<?php

namespace App\Filament\Resources\KategoriResource\Pages;

use App\Filament\Resources\KategoriResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;


class CreateKategori extends CreateRecord
{
    protected static string $resource = KategoriResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['nama']);

        return $data;
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
