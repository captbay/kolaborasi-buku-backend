<?php

namespace App\Filament\Resources\KeranjangResource\Widgets;

use App\Models\keranjang;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KeranjangOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // count % kenaikan total keranjang dibandingkan bulan lalu
        $totalKeranjangBulanLalu = Keranjang::whereMonth('created_at', date('m', strtotime('-1 month')))->count();
        $totalKeranjangBulanIni = Keranjang::whereMonth('created_at', date('m'))->count();

        if ($totalKeranjangBulanLalu == 0) {
            $totalKeranjangBulanLalu = 1;
        }
        $kenaikan = ($totalKeranjangBulanIni - $totalKeranjangBulanLalu) / $totalKeranjangBulanLalu * 100;


        return [
            Stat::make(
                'Total Keranjang Bulan Ini',
                Keranjang::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count()
            )
                ->description('Meningkat ' . $kenaikan . '% dari bulan lalu')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([$totalKeranjangBulanLalu, $totalKeranjangBulanIni]),
            Stat::make(
                'Total User Yang Ingin Beli',
                User::whereHas('keranjangs')->count()
            ),
            Stat::make(
                'Total Buku Yang Ingin Dibeli',
                Keranjang::with('buku_dijual')->count()
            ),
        ];
    }
}
