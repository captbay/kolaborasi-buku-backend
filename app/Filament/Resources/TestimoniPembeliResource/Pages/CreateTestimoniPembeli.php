<?php

namespace App\Filament\Resources\TestimoniPembeliResource\Pages;

use App\Filament\Resources\TestimoniPembeliResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTestimoniPembeli extends CreateRecord
{
    protected static string $resource = TestimoniPembeliResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }
}
