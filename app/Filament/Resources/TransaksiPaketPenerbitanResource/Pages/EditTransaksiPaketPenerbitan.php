<?php

namespace App\Filament\Resources\TransaksiPaketPenerbitanResource\Pages;

use App\Filament\Resources\TransaksiPaketPenerbitanResource;
use App\Models\jasa_tambahan;
use App\Models\paket_penerbitan;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiPaketPenerbitan extends EditRecord
{
    protected static string $resource = TransaksiPaketPenerbitanResource::class;

    protected static ?string $title = 'Sunting Buku';


    protected function mutateFormDataBeforeSave(array $data): array
    {
        // dd($this->record->trx_jasa_penerbitan->toArray());

        $total_harga = 0;
        // get harga paket selected
        $paketSelected = $data['nama'];
        $hargaPaket = paket_penerbitan::find($paketSelected)->harga;
        $total_harga += $hargaPaket;

        // get repeater harga jasa selected
        foreach ($this->record->trx_jasa_penerbitan->toArray() as $key => $value) {
            $hargaJasa = jasa_tambahan::find($value['jasa_tambahan_id'])->harga;
            $total_harga += $hargaJasa;
        }

        $data['total_harga'] = $total_harga;

        $no_transaksi = $this->record->no_transaksi;
        $id = $this->record->id;
        $member = $this->record->user;

        // send notification to user
        Notification::make()
            ->success()
            ->title('Buku Permohonan Terbit : No Transaksi ' . $no_transaksi . ' Sudah disunting oleh admin, mohon dicek kembali transaksi dan koleksi buku penerbitan Anda.')
            ->body($id)
            ->sendToDatabase($member);

        return $data;
    }

    // protected function getSavedNotification(): ?Notification
    // {
    // $recipientAdmin = auth()->user();

    // $id = $this->record->no_transaksi;
    // $member = $this->record->user;

    // // send notification to user
    // Notification::make()
    //     ->success()
    //     ->title('Permohonan Terbit Anda dengan ID ' . $id . ' Sudah Disunting')
    //     ->body('Silahkan Untuk Membayar DP 50% dari total harga')
    //     ->sendToDatabase($member);

    // return Notification::make()
    //     ->success()
    //     ->title('Permohonan Terbit dengan ID ' . $id . ' dari Member ' . $member->nama_lengkap . ' Sudah Disunting')
    //     ->body('Transaksi ini akan berlanjut ke status TERIMA DRAFT')
    //     ->sendToDatabase($recipientAdmin);
    // }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
