<?php

namespace App\Filament\Resources\UserBabBukuKolaborasiResource\Pages;

use App\Filament\Resources\UserBabBukuKolaborasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUserBabBukuKolaborasis extends ListRecords
{
    protected static string $resource = UserBabBukuKolaborasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Kolaborator Bab (tanpa bayar)')
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),
            'Uploaded' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'UPLOADED')),
            'Revisi' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'REVISI')),
            'Done' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'DONE')),
            'Rejected' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'REJECTED')),
        ];
    }
}