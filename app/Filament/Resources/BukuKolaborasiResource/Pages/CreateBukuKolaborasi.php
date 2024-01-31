<?php

namespace App\Filament\Resources\BukuKolaborasiResource\Pages;

use App\Filament\Resources\BukuKolaborasiResource;
use App\Models\bab_buku_kolaborasi;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBukuKolaborasi extends CreateRecord
{
    protected static string $resource = BukuKolaborasiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // protected function handleRecordCreation(array $data): Model
    // {
    //     // get repeter bab_items
    //     $bab_items = $this->bab_buku_kolaborasi;

    //     dd($bab_items);

    //     // count number of bab_items
    //     $bab_items_count = count($bab_items);

    //     // set $record jumlah_bab = $bab_items_count
    //     $data['jumlah_bab'] = $bab_items_count;

    //     // insert the bukudijual
    //     $record = static::getModel()::create($data);

    //     // for each bab_item repeter and create bab_buku_kolaborasi model
    //     for ($i = 0; $i < $bab_items_count; $i++) {
    //         $bab_item = $bab_items[$i];

    //         // create bab_buku_kolaborasi model
    //         bab_buku_kolaborasi::create([
    //             'buku_kolaborasi_id ' => $record->id,
    //             'no_bab' => $i + 1,
    //             'judul' => $bab_item['judul'],
    //             'harga' => $bab_item['harga'],
    //             'durasi_pembuatan' => $bab_item['durasi_pembuatan'],
    //             'deskripsi' => $bab_item['deskripsi'],
    //             'active_flag' => $bab_item['active_flag'],
    //         ]);
    //     }

    //     return $record;
    // }
}
