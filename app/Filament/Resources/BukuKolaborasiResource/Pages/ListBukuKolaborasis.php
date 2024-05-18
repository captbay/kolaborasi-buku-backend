<?php

namespace App\Filament\Resources\BukuKolaborasiResource\Pages;

use App\Filament\Resources\BukuKolaborasiResource;
use App\Models\bab_buku_kolaborasi;
use App\Models\buku_kolaborasi;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBukuKolaborasis extends ListRecords
{
    protected static string $resource = BukuKolaborasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make(),
            'Proses' => Tab::make()
                ->modifyQueryUsing(
                    function (buku_kolaborasi $buku_kolaborasi) {
                        $dude = $buku_kolaborasi->with([
                            'bab_buku_kolaborasi' => function ($query) {
                                $query->with([
                                    'user_bab_buku_kolaborasi' => function ($query) {
                                        $query->where('status', 'DONE');
                                    }
                                ]);
                            }
                        ]);

                        $dude->where('dijual', 0);

                        $dude->whereHas('bab_buku_kolaborasi.user_bab_buku_kolaborasi', function ($query) {
                            $query->where('status', 'DONE');
                        }, '=', $buku_kolaborasi->bab_buku_kolaborasi->count());

                        return $dude;
                    }
                ),
            'Siap Dijual' => Tab::make()
                ->modifyQueryUsing(
                    function (buku_kolaborasi $buku_kolaborasi) {
                        $dude = $buku_kolaborasi->with([
                            'bab_buku_kolaborasi.user_bab_buku_kolaborasi' => function ($query) {
                                $query->where('status', 'DONE');
                            }
                        ]);

                        $dude->where('dijual', 0);

                        $dude->whereHas('bab_buku_kolaborasi.user_bab_buku_kolaborasi', function ($query) {
                            $query->where('status', 'DONE');
                        });

                        $dude->whereDoesntHave('bab_buku_kolaborasi', function ($query) {
                            $query->whereDoesntHave('user_bab_buku_kolaborasi', function ($query) {
                                $query->where('status', 'DONE');
                            });
                        });

                        return $dude;
                    }


                ),
            'Sudah Dijual' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('dijual', 1)),

        ];
    }
}
