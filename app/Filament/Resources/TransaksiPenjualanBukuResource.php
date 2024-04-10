<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiPenjualanBukuResource\Pages;
use App\Models\buku_lunas_user;
use App\Models\transaksi_penjualan_buku;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransaksiPenjualanBukuResource extends Resource
{
    protected static ?string $model = transaksi_penjualan_buku::class;

    protected static ?string $navigationLabel = 'Transaksi Penjualan Buku';

    protected static ?string $label = 'Transaksi Penjualan Buku';

    protected static ?string $slug = 'transaksi-penjualan-buku';

    protected static ?string $title = 'Transaksi Penjualan Buku';

    // protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-s-currency-dollar';

    // navigation groups

    protected static ?string $navigationGroup = 'Buku Dijual';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('created_at')
                                    ->label('Tanggal Dibuat Transaksi'),
                                Forms\Components\TextInput::make('date_time_lunas')
                                    ->label('Tanggal Lunas'),
                                Forms\Components\TextInput::make('no_transaksi'),
                                Forms\Components\FileUpload::make('foto_bukti_bayar')
                                    ->label('Bukti Bayar')
                                    ->openable()
                                    ->downloadable()
                                    ->columnSpan('full')
                                    ->disabled(),
                                Forms\Components\Fieldset::make('Data Pembeli')
                                    ->relationship('user')
                                    ->schema([
                                        Forms\Components\TextInput::make('nama_lengkap'),
                                        Forms\Components\TextInput::make('email'),
                                        Forms\Components\TextInput::make('no_telepon'),
                                        Forms\Components\TextInput::make('alamat'),
                                    ])
                            ])
                            ->columns(3),

                        Forms\Components\Repeater::make('list_transaksi_buku')
                            ->relationship()
                            ->schema([
                                Forms\Components\Fieldset::make()
                                    ->relationship('buku_dijual')
                                    ->schema([
                                        Forms\Components\FileUpload::make('cover_buku')
                                            ->disabled()
                                            ->openable()
                                            ->downloadable(),

                                        Forms\Components\TextInput::make('judul'),

                                        Forms\Components\Select::make('kategori')
                                            ->relationship('kategori', 'nama')
                                            ->label('Kategori Buku'),

                                        Forms\Components\TextInput::make('penerbit'),

                                        Forms\Components\TextInput::make('harga'),
                                    ]),
                            ])
                            ->hiddenLabel(),
                        Forms\Components\TextInput::make('total_harga'),

                    ])
                    ->columnSpan(['lg' => 2]),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_transaksi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.nama_lengkap')
                    ->label('Nama Lengkap')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('list_transaksi_buku')
                    ->label('Jumlah Buku')
                    ->default('Tidak ada buku')
                    ->searchable()
                    ->formatStateUsing(function ($state, transaksi_penjualan_buku $transaksi_penjualan_buku) {
                        return $transaksi_penjualan_buku->list_transaksi_buku->count() . ' buku';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga')
                    ->searchable()
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(
                        fn (transaksi_penjualan_buku $transaksi_penjualan_buku) => match ($transaksi_penjualan_buku->status) {
                            'PROGRESS' => 'primary',
                            'DONE' => 'success',
                            'FAILED' => 'danger',
                            'UPLOADED' => 'info',
                        }
                    )
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Detail')->slideOver(),
                // verifikasi
                Tables\Actions\Action::make('Verifikasi')
                    ->hidden(function (transaksi_penjualan_buku $transaksi) {
                        if ($transaksi->foto_bukti_bayar == null) {
                            return true;
                        }

                        if ($transaksi->status != 'UPLOADED') {
                            return true;
                        }
                        return false;
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Pembayaran')
                    ->modalDescription('Apakah anda yakin ingin memverifikasi pembayaran ini, mohon cek dahulu bukti bayar?')
                    ->modalSubmitActionLabel('iya, verifikasi')
                    ->color('success')
                    ->modalIcon('heroicon-s-chat-bubble-left-ellipsis')
                    ->icon('heroicon-s-document-check')
                    ->modalIconColor('success')
                    ->form([
                        Forms\Components\FileUpload::make('foto_bukti_bayar')
                            ->label('Foto Bukti Bayar')
                            ->default(fn (transaksi_penjualan_buku $transaksi) => $transaksi->foto_bukti_bayar)
                            ->openable()
                            ->downloadable()
                            ->image()
                            ->imagePreviewHeight('500')
                            ->panelAspectRatio('1:1')
                            ->disabled(),
                    ])
                    ->action(
                        function (transaksi_penjualan_buku $transaksi): void {
                            if ($transaksi->date_time_lunas != null) {
                                Notification::make()
                                    ->danger()
                                    ->title('Customer ini sudah lunas')
                                    ->send();

                                return;
                            }

                            if ($transaksi->foto_bukti_bayar == null) {
                                Notification::make()
                                    ->danger()
                                    ->title('Foto bukti bayar tidak ada')
                                    ->send();
                            }

                            // update status and datetimelunas
                            $transaksi->update([
                                'status' => 'DONE',
                                'date_time_lunas' => Carbon::now(),
                            ]);

                            // foreach $transaksi->list_transaksi_buku to create buku_lunas_user
                            foreach ($transaksi->list_transaksi_buku as $buku) {
                                buku_lunas_user::create([
                                    'user_id' => $transaksi->user_id,
                                    'buku_dijual_id' => $buku['buku_dijual_id']
                                ]);
                            }

                            $recipientAdmin = auth()->user();

                            Notification::make()
                                ->success()
                                ->title(
                                    'Transaksi Pembelian Buku Berhasil'
                                )
                                ->body('Transaksi pembelian buku #' . $transaksi->no_transaksi . ' berhasil diverifikasi, ' . $transaksi->user->nama_lengkap . ' sudah mendapatkan buku yang dibeli')
                                ->sendToDatabase($recipientAdmin)
                                ->send();

                            $recipientUser = $transaksi->user;

                            // send notif to user yang bayar
                            Notification::make()
                                ->success()
                                ->title(
                                    'Transaksi Pembelian Buku Berhasil'
                                )
                                ->body(
                                    'Transaksi pembelian buku dengan nomor transaksi #' . $transaksi->no_transaksi . ' berhasil diverifikasi, terimakasih sudah berbelanja di penerbitan kami.'
                                )
                                ->sendToDatabase($recipientUser);

                            event(new DatabaseNotificationsSent($recipientUser));

                            return;
                        }
                    ),
                // batalkan verifikasi
                Tables\Actions\Action::make('Gagal')
                    ->hidden(function (transaksi_penjualan_buku $transaksi) {
                        if ($transaksi->status != 'UPLOADED') {
                            return true;
                        }
                        return false;
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Verifikasi Pembayaran')
                    ->modalDescription('Apakah anda yakin ingin membatalkan verifikasi pembayaran ini?')
                    ->modalSubmitActionLabel('iya, batalkan')
                    ->color('danger')
                    ->modalIcon('heroicon-s-chat-bubble-left-ellipsis')
                    ->icon('heroicon-s-document-check')
                    ->modalIconColor('danger')
                    ->form([
                        Forms\Components\FileUpload::make('foto_bukti_bayar')
                            ->label('Foto Bukti Bayar')
                            ->default(fn (transaksi_penjualan_buku $transaksi) => $transaksi->foto_bukti_bayar)
                            ->openable()
                            ->downloadable()
                            ->image()
                            ->imagePreviewHeight('500')
                            ->panelAspectRatio('1:1')
                            ->disabled(),
                    ])
                    ->action(
                        function (transaksi_penjualan_buku $transaksi): void {
                            if ($transaksi->date_time_lunas != null) {
                                Notification::make()
                                    ->danger()
                                    ->title('Customer ini sudah lunas')
                                    ->send();

                                return;
                            }

                            // update status and datetimelunas
                            $transaksi->update([
                                'date_time_exp' => null,
                                'status' => 'FAILED',
                            ]);

                            $recipientAdmin = auth()->user();

                            Notification::make()
                                ->success()
                                ->title(
                                    'Transaksi Pembelian Buku Gagal'
                                )
                                ->body('Transaksi pembelian buku #' . $transaksi->no_transaksi . ' berhasil digagalkan')
                                ->sendToDatabase($recipientAdmin)
                                ->send();

                            $recipientUser = $transaksi->user;

                            // send notif to user yang bayar
                            Notification::make()
                                ->success()
                                ->title(
                                    'Transaksi Pembelian Buku Gagal'
                                )
                                ->body(
                                    'Transaksi pembelian buku dengan nomor transaksi #' . $transaksi->no_transaksi . ' gagal, silahkan mengulangi pembelian buku.'
                                )
                                ->sendToDatabase($recipientUser);

                            event(new DatabaseNotificationsSent($recipientUser));

                            return;
                        }
                    ),

            ])
            ->recordUrl(false)
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksiPenjualanBukus::route('/'),
            // 'create' => Pages\CreateTransaksiPenjualanBuku::route('/create'),
            // 'edit' => Pages\EditTransaksiPenjualanBuku::route('/{record}/edit'),
        ];
    }
}
