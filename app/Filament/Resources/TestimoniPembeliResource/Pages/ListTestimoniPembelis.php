<?php

namespace App\Filament\Resources\TestimoniPembeliResource\Pages;

use App\Filament\Resources\TestimoniPembeliResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTestimoniPembelis extends ListRecords
{
    protected static string $resource = TestimoniPembeliResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All Rating' => Tab::make(),
            '1' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('rating', 1)),
            '2' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('rating', 2)),
            '3' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('rating', 3)),
            '4' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('rating', 4)),
            '5' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('rating', 5)),
        ];
    }
}
