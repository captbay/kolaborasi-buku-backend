<?php

namespace App\Filament\Resources\MouResource\Pages;

use App\Filament\Resources\MouResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMous extends ListRecords
{
    protected static string $resource = MouResource::class;

    protected ?string $heading = 'MOU';

    protected ?string $subheading = '*Silahkan segera membuat data MOU kolaborasi dan MOU penerbitan. Yang akan ditampilkankan adalah data terbaru.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
