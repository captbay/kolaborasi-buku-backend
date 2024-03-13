<?php

namespace App\Filament\Widgets;

use App\Models\transaksi_kolaborasi_buku;
use App\Models\transaksi_paket_penerbitan;
use App\Models\transaksi_penjualan_buku;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        // kalo tidak ada default 30 hari terakhir
        $tanggalMulai = !is_null($this->filters['tanggalMulai'] ?? null) ?
            Carbon::parse($this->filters['tanggalMulai']) :
            now()->subDays(30);

        $tanggalSelesai = !is_null($this->filters['tanggalSelesai'] ?? null) ?
            Carbon::parse($this->filters['tanggalSelesai']) :
            now();

        // temp tanggal
        $tempTanggalMulai = $tanggalMulai->copy();
        $tempTanggalSelesai = $tanggalSelesai->copy();

        $diffInDays = $tanggalMulai->diffInDays($tanggalSelesai);

        /*
        *  count revenue from get all data in transaksi where status done and between date time lunas
        */
        $revenue = 0;
        $revenuePenjualanBuku = 0;
        $revenueKolaborasiBuku = 0;
        $revenuePaketPenerbitan = 0;

        $transaksiPenjualanBuku = transaksi_penjualan_buku::where('status', 'DONE')
            ->whereBetween('date_time_lunas', [$tanggalMulai, $tempTanggalSelesai->copy()->addDay()])
            ->get('total_harga');
        foreach ($transaksiPenjualanBuku as $transaksi) {
            $revenuePenjualanBuku += $transaksi->total_harga;
        }

        // perhitungan manual
        $revenue += $revenuePenjualanBuku;

        $transaksiKolaborasiBuku = transaksi_kolaborasi_buku::where('status', 'DONE')
            ->whereBetween('date_time_lunas', [$tanggalMulai, $tempTanggalSelesai->copy()->addDay()])
            ->get('total_harga');
        foreach ($transaksiKolaborasiBuku as $transaksi) {
            $revenueKolaborasiBuku += $transaksi->total_harga;
        }

        // perhitungan manual
        $revenue += $revenueKolaborasiBuku;

        $transaksiPaketPenerbitan = transaksi_paket_penerbitan::where('status', 'DONE')
            ->whereBetween('date_time_lunas', [$tanggalMulai, $tempTanggalSelesai->copy()->addDay()])
            ->get('total_harga');
        foreach ($transaksiPaketPenerbitan as $transaksi) {
            $revenue += $transaksi->total_harga;
            $revenuePaketPenerbitan += $transaksi->total_harga;
        }

        // perhitungan manual
        $revenue += $revenuePaketPenerbitan;
        /*
        *  count newUsers from get all data in Users where email_verified_at between date time lunas
        */

        $users = User::whereNot('role', 'ADMIN')
            ->where('email_verified_at', '!=', null)
            ->whereBetween('email_verified_at', [$tanggalMulai, $tempTanggalSelesai->copy()->addDay()]);
        $countUsers = $users->count();
        foreach ($users as $user) {
            $arrayUsers[] = $user;
        }

        /*
        *  count revenue from get all data in transaksi where status done and between date time lunas
        */

        // get data for chart per date in range $tanggalMulai and $tanggalSelesai
        $chartRevenue = [];

        for ($i = 0; $i <= $diffInDays; $i++) {
            if ($i == 0) {
                $tanggal = $tanggalMulai;
            } else {
                $tanggal = $tempTanggalMulai->copy()->addDay();
            }
            $totalPerDay = 0;
            $transaksiPenjualanBuku = transaksi_penjualan_buku::where('status', 'DONE')
                ->whereDate('date_time_lunas', $tanggal)
                ->get('total_harga');
            foreach ($transaksiPenjualanBuku as $transaksi) {
                $totalPerDay += $transaksi->total_harga;
            }
            $transaksiKolaborasiBuku = transaksi_kolaborasi_buku::where('status', 'DONE')
                ->whereDate('date_time_lunas', $tanggal)
                ->get('total_harga');
            foreach ($transaksiKolaborasiBuku as $transaksi) {
                $totalPerDay += $transaksi->total_harga;
            }
            $transaksiPaketPenerbitan = transaksi_paket_penerbitan::where('status', 'DONE')
                ->whereDate('date_time_lunas', $tanggal)
                ->get('total_harga');
            foreach ($transaksiPaketPenerbitan as $transaksi) {
                $totalPerDay += $transaksi->total_harga;
            }
            $chartRevenue[] = $totalPerDay;
        }

        // count percentage of increase or decrease revenue dari 30 day lalu dimana sebulan sebelum $tanggalMulai
        $revenueLastMonth = 0;

        $transaksiPenjualanBuku = transaksi_penjualan_buku::where('status', 'DONE')
            ->whereBetween('date_time_lunas', [$tempTanggalMulai->copy()->subDays(30), $tanggalMulai])
            ->get('total_harga');
        foreach ($transaksiPenjualanBuku as $transaksi) {
            $revenueLastMonth += $transaksi->total_harga;
        }
        $transaksiKolaborasiBuku = transaksi_kolaborasi_buku::where('status', 'DONE')
            ->whereBetween('date_time_lunas', [$tempTanggalMulai->copy()->subDays(30), $tanggalMulai])
            ->get('total_harga');
        foreach ($transaksiKolaborasiBuku as $transaksi) {
            $revenueLastMonth += $transaksi->total_harga;
        }
        $transaksiPaketPenerbitan = transaksi_paket_penerbitan::where('status', 'DONE')
            ->whereBetween('date_time_lunas', [$tempTanggalMulai->copy()->subDays(30), $tanggalMulai])
            ->get('total_harga');
        foreach ($transaksiPaketPenerbitan as $transaksi) {
            $revenueLastMonth += $transaksi->total_harga;
        }
        $kenaikan = $revenue - $revenueLastMonth;
        $revenueLastMonth = $revenueLastMonth == 0 ? 1 : $revenueLastMonth;
        $percentageRevenue = ($kenaikan / $revenueLastMonth) * 100;

        $formatNumberRupiah = function ($number): string {
            // format uang dalam rupiah agar tidak panjang 0 nya
            return 'Rp' . number_format($number, 0, ',', '.');
        };

        /*
        *  count newUsers from get all data in Users where email_verified_at between date time lunas
        */
        // get data for chart per date in range $tanggalMulai and $tanggalSelesai
        $chartUsers = [];
        for ($i = 0; $i <= $diffInDays; $i++) {
            if ($i == 0) {
                $tanggal = $tanggalMulai;
            } else {
                $tanggal = $tempTanggalMulai->copy()->addDay();
            }
            $countPerDay = User::whereDate('email_verified_at', $tanggal)
                ->whereNot('role', 'ADMIN')
                ->count();
            $chartUsers[] = $countPerDay;
        }

        // count percentage of increase or decrease users dari bulan lalu dimana sebulan sebelum $tanggalMulai
        $usersLastMonth = User::whereBetween('email_verified_at', [$tempTanggalMulai->copy()->subDays(30), $tanggalMulai])
            ->whereNot('role', 'ADMIN');
        $countUsersLastMonth = $usersLastMonth->count();
        $kenaikan = $countUsers - $countUsersLastMonth;
        $countUsersLastMonth = $countUsersLastMonth == 0 ? 1 : $countUsersLastMonth;
        $percentageUsers = ($kenaikan / $countUsersLastMonth) * 100;

        return [
            Stat::make('Total Pendapatan Penjualan Buku', $formatNumberRupiah($revenuePenjualanBuku)),
            // ->description($percentageRevenue . ($percentageRevenue > 0 ? '% dari 30 hari sebelum' : '% dari 30 hari sebelum'))
            // ->descriptionIcon($percentageRevenue > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            // ->chart($chartRevenue)
            // ->color($percentageRevenue > 0 ? 'success' : 'danger'),
            Stat::make('Total Pendapatan Kolaborasi Buku', $formatNumberRupiah($revenueKolaborasiBuku)),
            // ->description($percentageRevenue . ($percentageRevenue > 0 ? '% dari 30 hari sebelum' : '% dari 30 hari sebelum'))
            // ->descriptionIcon($percentageRevenue > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            // ->chart($chartRevenue)
            // ->color($percentageRevenue > 0 ? 'success' : 'danger'),
            Stat::make('Total Pendapatan Paket Penerbitan', $formatNumberRupiah($revenuePaketPenerbitan)),
            // ->description($percentageRevenue . ($percentageRevenue > 0 ? '% dari 30 hari sebelum' : '% dari 30 hari sebelum'))
            // ->descriptionIcon($percentageRevenue > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            // ->chart($chartRevenue)
            // ->color($percentageRevenue > 0 ? 'success' : 'danger'),
            Stat::make('Total Pendapatan Keseluruhan', $formatNumberRupiah($revenue))
                ->description($percentageRevenue . ($percentageRevenue > 0 ? '% dari 30 hari sebelum' : '% dari 30 hari sebelum'))
                ->descriptionIcon($percentageRevenue > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($chartRevenue)
                ->color($percentageRevenue > 0 ? 'success' : 'danger'),
            Stat::make('User Baru', $countUsers)
                ->description($percentageUsers . ($percentageUsers > 0 ? '% dari 30 hari sebelum' : '% dari 30 hari sebelum'))
                ->descriptionIcon($percentageUsers > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($chartUsers)
                ->color($percentageUsers > 0 ? 'success' : 'danger'),
        ];
    }
}