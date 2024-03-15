<?php

namespace App\Filament\Resources\JasaTambahanResource\Pages;

use App\Filament\Resources\JasaTambahanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJasaTambahan extends EditRecord
{
    protected static string $resource = JasaTambahanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
