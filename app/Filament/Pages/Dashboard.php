<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-s-home';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $label = 'Dashboard';

    protected static ?string $title = 'Dashboard';

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Rekap Laporan (default: 30 hari terakhir)')
                    ->schema([
                        DatePicker::make('tanggalMulai')
                            ->native(false)
                            ->maxDate(fn (Get $get) => $get('tanggalSelesai') ?: now()),
                        DatePicker::make('tanggalSelesai')
                            ->native(false)
                            ->minDate(fn (Get $get) => $get('tanggalMulai') ?: now())
                            ->maxDate(now()),
                    ])
                    ->columns(2),
            ]);
    }
}