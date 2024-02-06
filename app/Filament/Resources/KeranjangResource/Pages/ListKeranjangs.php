<?php

namespace App\Filament\Resources\KeranjangResource\Pages;

use App\Filament\Resources\KeranjangResource;
use App\Filament\Resources\KeranjangResource\Widgets\KeranjangOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKeranjangs extends ListRecords
{
    protected static string $resource = KeranjangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            KeranjangOverview::class,
        ];
    }
}
