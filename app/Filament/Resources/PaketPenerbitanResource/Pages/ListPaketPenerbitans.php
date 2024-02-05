<?php

namespace App\Filament\Resources\PaketPenerbitanResource\Pages;

use App\Filament\Resources\PaketPenerbitanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaketPenerbitans extends ListRecords
{
    protected static string $resource = PaketPenerbitanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
