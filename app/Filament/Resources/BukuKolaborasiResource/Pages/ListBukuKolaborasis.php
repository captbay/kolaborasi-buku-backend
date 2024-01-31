<?php

namespace App\Filament\Resources\BukuKolaborasiResource\Pages;

use App\Filament\Resources\BukuKolaborasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBukuKolaborasis extends ListRecords
{
    protected static string $resource = BukuKolaborasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
