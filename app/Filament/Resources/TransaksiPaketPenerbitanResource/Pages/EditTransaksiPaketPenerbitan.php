<?php

namespace App\Filament\Resources\TransaksiPaketPenerbitanResource\Pages;

use App\Filament\Resources\TransaksiPaketPenerbitanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiPaketPenerbitan extends EditRecord
{
    protected static string $resource = TransaksiPaketPenerbitanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
