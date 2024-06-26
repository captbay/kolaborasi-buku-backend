<?php

namespace App\Filament\Widgets;

use App\Models\transaksi_kolaborasi_buku;
use App\Models\transaksi_paket_penerbitan;
use App\Models\transaksi_penjualan_buku;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan Per Tahun ini';

    protected static ?int $sort = 1;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $revenue = [];

        for ($i = 1; $i <= 12; $i++) {
            $buku = transaksi_penjualan_buku::where('status', 'DONE')
                ->whereMonth('date_time_lunas', $i)
                ->sum('total_harga');

            $kolaborasi = transaksi_kolaborasi_buku::where('status', 'DONE')
                ->whereMonth('date_time_lunas', $i)
                ->sum('total_harga');

            $paket = transaksi_paket_penerbitan::where('status', 'DONE')
                ->whereMonth('date_time_lunas', $i)
                ->sum('total_harga');

            $revenue[] = $buku + $kolaborasi + $paket;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan Total Per Bulan',
                    'data' => $revenue,
                    'fill' => 'start',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }
}
