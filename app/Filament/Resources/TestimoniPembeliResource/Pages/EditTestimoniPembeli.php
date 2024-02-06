<?php

namespace App\Filament\Resources\TestimoniPembeliResource\Pages;

use App\Filament\Resources\TestimoniPembeliResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTestimoniPembeli extends EditRecord
{
    protected static string $resource = TestimoniPembeliResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
