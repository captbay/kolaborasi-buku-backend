<?php

namespace App\Filament\Resources\JasaTambahanResource\Pages;

use App\Filament\Resources\JasaTambahanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJasaTambahans extends ListRecords
{
    protected static string $resource = JasaTambahanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
