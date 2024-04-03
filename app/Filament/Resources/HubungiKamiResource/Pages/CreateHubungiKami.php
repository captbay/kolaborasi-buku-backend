<?php

namespace App\Filament\Resources\HubungiKamiResource\Pages;

use App\Filament\Resources\HubungiKamiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHubungiKami extends CreateRecord
{
    protected static string $resource = HubungiKamiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
