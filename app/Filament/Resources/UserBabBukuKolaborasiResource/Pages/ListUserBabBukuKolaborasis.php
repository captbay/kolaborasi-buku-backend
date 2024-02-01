<?php

namespace App\Filament\Resources\UserBabBukuKolaborasiResource\Pages;

use App\Filament\Resources\UserBabBukuKolaborasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserBabBukuKolaborasis extends ListRecords
{
    protected static string $resource = UserBabBukuKolaborasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Kolaborator Bab (tanpa bayar)')
        ];
    }
}
