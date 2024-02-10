<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiPaketPenerbitanResource\Pages;
use App\Filament\Resources\TransaksiPaketPenerbitanResource\RelationManagers;
use App\Models\transaksi_paket_penerbitan;
use App\Models\TransaksiPaketPenerbitan;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransaksiPaketPenerbitanResource extends Resource
{
    protected static ?string $model = transaksi_paket_penerbitan::class;

    protected static ?string $navigationLabel = 'Transaksi Paket Penerbitan';

    protected static ?string $label = 'Transaksi Paket Penerbitan';

    protected static ?string $slug = 'transaksi-paket-penerbitan';

    protected static ?string $title = 'Transaksi Paket Penerbitan';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-s-currency-dollar';

    // navigation groups

    protected static ?string $navigationGroup = 'Buku Permohonan Terbit';

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

                        Forms\Components\Fieldset::make('Data Buku Permohonan Terbit')
                            ->relationship('buku_permohonan_terbit')
                            ->schema([
                                Forms\Components\TextInput::make('judul')
                                    ->label('Judul Buku'),

                                Forms\Components\TextInput::make('persen_bagi_hasil')
                                    ->label('Persen Bagi Hasil (%)'),

                                Forms\Components\Textarea::make('deskripsi')
                                    ->columnSpan('full'),

                                Forms\Components\FileUpload::make('file_buku')
                                    ->disabled()
                                    ->openable()
                                    ->columnSpan('full')
                                    ->downloadable(),
                            ]),

                        Forms\Components\Fieldset::make('Data Paket Penerbitan')
                            ->relationship('paket_penerbitan')
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Paket'),

                                Forms\Components\TextInput::make('harga')
                                    ->label('Harga Paket'),

                                Forms\Components\Textarea::make('deskripsi')
                                    ->columnSpan('full'),

                                Forms\Components\DatePicker::make('waktu_mulai'),

                                Forms\Components\DatePicker::make('waktu_selesai'),
                            ]),

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
                Tables\Columns\TextColumn::make('paket_penerbitan.nama')
                    ->label('Paket Penerbitan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buku_permohonan_terbit.judul')
                    ->label('Judul Buku')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga')
                    ->searchable()
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(
                        fn (transaksi_paket_penerbitan $transaksi_paket_penerbitan) => match ($transaksi_paket_penerbitan->status) {
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
                    ->hidden(function (transaksi_paket_penerbitan $transaksi) {
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
                            ->default(fn (transaksi_paket_penerbitan $transaksi) => $transaksi->foto_bukti_bayar)
                            ->openable()
                            ->downloadable()
                            ->image()
                            ->imagePreviewHeight('500')
                            ->panelAspectRatio('1:1')
                            ->disabled(),
                    ])
                    ->action(
                        function (transaksi_paket_penerbitan $transaksi): void {
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

                            Notification::make()
                                ->success()
                                ->title('Transaksi berhasil diverifikasi')
                                ->send();

                            return;
                        }
                    ),
                // batalkan verifikasi
                Tables\Actions\Action::make('Gagal')
                    ->hidden(function (transaksi_paket_penerbitan $transaksi) {
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
                            ->default(fn (transaksi_paket_penerbitan $transaksi) => $transaksi->foto_bukti_bayar)
                            ->openable()
                            ->downloadable()
                            ->image()
                            ->imagePreviewHeight('500')
                            ->panelAspectRatio('1:1')
                            ->disabled(),
                    ])
                    ->action(
                        function (transaksi_paket_penerbitan $transaksi): void {
                            if ($transaksi->date_time_lunas != null) {
                                Notification::make()
                                    ->danger()
                                    ->title('Customer ini sudah lunas')
                                    ->send();

                                return;
                            }

                            // update status and datetimelunas
                            $transaksi->update([
                                'status' => 'FAILED',
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Transaksi berhasil dibatalkan')
                                ->send();

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
            'index' => Pages\ListTransaksiPaketPenerbitans::route('/'),
            // 'create' => Pages\CreateTransaksiPaketPenerbitan::route('/create'),
            // 'edit' => Pages\EditTransaksiPaketPenerbitan::route('/{record}/edit'),
        ];
    }
}
