<?php

namespace App\Filament\Resources\PaketPenerbitanResource\Pages;

use App\Filament\Resources\PaketPenerbitanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaketPenerbitan extends EditRecord
{
    protected static string $resource = PaketPenerbitanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
