<?php

namespace App\Filament\Resources\BukuDijualResource\Pages;

use App\Filament\Resources\BukuDijualResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBukuDijual extends EditRecord
{
    protected static string $resource = BukuDijualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
