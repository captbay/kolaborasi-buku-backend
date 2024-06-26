<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserChart extends ChartWidget
{
    protected static ?string $heading = 'User Per Tahun ini';

    protected static ?int $sort = 1;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $users = [];

        for ($i = 1; $i <= 12; $i++) {
            $user = User::where('role', '!=', 'ADMIN')
                ->whereMonth('created_at', $i)
                ->count();

            $users[] = $user;
        }

        return [
            'datasets' => [
                [
                    'label' => 'User Total Per Bulan',
                    'data' => $users,
                    'fill' => 'start',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }
}
