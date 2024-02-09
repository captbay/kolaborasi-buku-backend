<?php

namespace App\Filament\Resources\TransaksiPenjualanBukuResource\Pages;

use App\Filament\Resources\TransaksiPenjualanBukuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiPenjualanBuku extends EditRecord
{
    protected static string $resource = TransaksiPenjualanBukuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
