<?php

namespace App\Filament\Resources\UserBabBukuKolaborasiResource\Pages;

use App\Filament\Resources\UserBabBukuKolaborasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserBabBukuKolaborasi extends EditRecord
{
    protected static string $resource = UserBabBukuKolaborasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
