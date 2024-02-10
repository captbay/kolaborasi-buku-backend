<?php

namespace App\Filament\Resources\TransaksiKolaborasiBukuResource\Pages;

use App\Filament\Resources\TransaksiKolaborasiBukuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiKolaborasiBuku extends EditRecord
{
    protected static string $resource = TransaksiKolaborasiBukuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
