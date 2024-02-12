<?php

namespace App\Filament\Resources\BukuPermohonanTerbitResource\Pages;

use App\Filament\Resources\BukuPermohonanTerbitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBukuPermohonanTerbits extends ListRecords
{
    protected static string $resource = BukuPermohonanTerbitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),
            'Review' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'REVIEW')),
            'Revisi' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'REVISI')),
            'Rejected' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'REJECTED')),
            'Accepted' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'ACCEPTED')),
        ];
    }
}
