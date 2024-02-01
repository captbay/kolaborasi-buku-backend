<?php

namespace App\Filament\Resources\UserBabBukuKolaborasiResource\Pages;

use App\Filament\Resources\UserBabBukuKolaborasiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserBabBukuKolaborasi extends CreateRecord
{
    protected static string $resource = UserBabBukuKolaborasiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
