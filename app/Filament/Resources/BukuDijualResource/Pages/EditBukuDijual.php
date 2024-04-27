<?php

namespace App\Filament\Resources\BukuDijualResource\Pages;

use App\Filament\Resources\BukuDijualResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class EditBukuDijual extends EditRecord
{
    protected static string $resource = BukuDijualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    // get all the name_generate
                    $name_generate = $record->storage_buku_dijual()->get('nama_generate');
                    // foreach name_generate
                    foreach ($name_generate as $name) {
                        // delete the file
                        Storage::disk('public')->delete($name['nama_generate']);
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['slug'] = Str::slug($data['judul']);
        $data['penerbit'] = config('app.app_name');

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
