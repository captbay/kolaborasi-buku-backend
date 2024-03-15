<?php

namespace App\Filament\Resources\BukuKolaborasiResource\Pages;

use App\Filament\Resources\BukuKolaborasiResource;
use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Resources\Pages\EditRecord;

class EditBukuKolaborasi extends EditRecord
{
    protected static string $resource = BukuKolaborasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug'] = Str::slug($data['judul']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
