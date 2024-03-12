<?php

namespace App\Filament\Resources\TransaksiPaketPenerbitanResource\Pages;

use App\Filament\Resources\TransaksiPaketPenerbitanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTransaksiPaketPenerbitans extends ListRecords
{
    protected static string $resource = TransaksiPaketPenerbitanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make(),
            'Progress' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'PROGRESS')),
            'Uploaded' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'UPLOADED')),
            'Failed' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'FAILED')),
            'Done' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'DONE')),
        ];
    }
}