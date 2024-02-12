<?php

namespace App\Filament\Resources\BukuLunasUserResource\Pages;

use App\Filament\Resources\BukuLunasUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBukuLunasUser extends EditRecord
{
    protected static string $resource = BukuLunasUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
