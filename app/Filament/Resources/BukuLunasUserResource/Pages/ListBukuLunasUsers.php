<?php

namespace App\Filament\Resources\BukuLunasUserResource\Pages;

use App\Filament\Resources\BukuLunasUserResource;
use App\Filament\Resources\BukuLunasUserResource\Widgets\BukuLunasOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBukuLunasUsers extends ListRecords
{
    protected static string $resource = BukuLunasUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
