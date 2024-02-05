<?php

namespace App\Filament\Resources\BukuPermohonanTerbitResource\Pages;

use App\Filament\Resources\BukuPermohonanTerbitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBukuPermohonanTerbit extends CreateRecord
{
    protected static string $resource = BukuPermohonanTerbitResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
