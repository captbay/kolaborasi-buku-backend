<?php

namespace App\Filament\Resources\BukuPermohonanTerbitResource\Pages;

use App\Filament\Resources\BukuPermohonanTerbitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBukuPermohonanTerbit extends EditRecord
{
    protected static string $resource = BukuPermohonanTerbitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
