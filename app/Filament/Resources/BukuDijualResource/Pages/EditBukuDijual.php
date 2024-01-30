<?php

namespace App\Filament\Resources\BukuDijualResource\Pages;

use App\Filament\Resources\BukuDijualResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

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
}
