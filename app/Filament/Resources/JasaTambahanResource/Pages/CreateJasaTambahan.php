<?php

namespace App\Filament\Resources\JasaTambahanResource\Pages;

use App\Filament\Resources\JasaTambahanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJasaTambahan extends CreateRecord
{
    protected static string $resource = JasaTambahanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
