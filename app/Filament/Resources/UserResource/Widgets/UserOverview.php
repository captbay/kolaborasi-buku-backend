<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::where('role', '!=', 'ADMIN')->count()),
            Stat::make('Total Member', User::where('role', 'MEMBER')->count()),
            Stat::make('Total Customer', User::where('role', 'CUSTOMER')->count()),
        ];
    }
}
