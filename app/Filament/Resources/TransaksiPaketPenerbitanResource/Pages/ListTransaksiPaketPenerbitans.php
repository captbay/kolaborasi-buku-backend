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
            'Review' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'REVIEW')),
            'Terima Draft' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'TERIMA DRAFT')),
            'DP Uploaded' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'DP UPLOADED')),
            'DP Tidak Sesuai' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'DP TIDAK SAH')),
            'Input ISBN' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'INPUT ISBN')),
            'Draft Selesai' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'DRAFT SELESAI')),
            'Pelunasan Uploaded' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'PELUNASAN UPLOADED')),
            'Pelunasan Tidak Sesuai' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'PELUNASAN TIDAK SAH')),
            'Siap Terbit' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'SIAP TERBIT')),
            'Sudah Terbit' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'SUDAH TERBIT')),
        ];
    }
}
