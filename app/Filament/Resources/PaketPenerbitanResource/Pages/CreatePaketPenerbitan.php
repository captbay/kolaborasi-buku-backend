<?php

namespace App\Filament\Resources\PaketPenerbitanResource\Pages;

use App\Filament\Resources\PaketPenerbitanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePaketPenerbitan extends CreateRecord
{
    protected static string $resource = PaketPenerbitanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
