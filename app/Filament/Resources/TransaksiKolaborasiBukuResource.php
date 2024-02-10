<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiKolaborasiBukuResource\Pages;
use App\Filament\Resources\TransaksiKolaborasiBukuResource\RelationManagers;
use App\Models\transaksi_kolaborasi_buku;
use App\Models\TransaksiKolaborasiBuku;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransaksiKolaborasiBukuResource extends Resource
{
    protected static ?string $model = transaksi_kolaborasi_buku::class;

    protected static ?string $navigationLabel = 'Transaksi Kolaborasi Buku';

    protected static ?string $label = 'Transaksi Kolaborasi Buku';

    protected static ?string $slug = 'transaksi-kolaborasi-buku';

    protected static ?string $title = 'Transaksi Kolaborasi Buku';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-s-currency-dollar';

    // navigation groups

    protected static ?string $navigationGroup = 'Buku Kolaborasi';

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

                        Forms\Components\Fieldset::make('Data Buku Kolaborasi')
                            ->relationship('bab_buku_kolaborasi')
                            ->schema([
                                Forms\Components\Fieldset::make()
                                    ->relationship('buku_kolaborasi')
                                    ->schema([
                                        Forms\Components\FileUpload::make('cover_buku')
                                            ->disabled()
                                            ->openable()
                                            ->columnSpan('full')
                                            ->downloadable(),

                                        Forms\Components\TextInput::make('judul')
                                            ->label('Judul Buku'),

                                        Forms\Components\Select::make('kategori.nama')
                                            ->relationship('kategori', 'nama')
                                            ->label('Kategori Buku'),
                                    ]),

                                Forms\Components\TextInput::make('no_bab'),
                                Forms\Components\TextInput::make('judul')
                                    ->label('Judul Bab'),
                                Forms\Components\TextInput::make('durasi_pembuatan')
                                    ->label('Durasi Pembuatan (hari)'),
                                Forms\Components\Textarea::make('deskripsi'),
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
                Tables\Columns\TextColumn::make('bab_buku_kolaborasi.buku_kolaborasi.judul')
                    ->label('Judul Buku')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bab_buku_kolaborasi.no_bab')
                    ->label('No Bab')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bab_buku_kolaborasi.judul')
                    ->label('Judul Bab')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga')
                    ->searchable()
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(
                        fn (transaksi_kolaborasi_buku $transaksi_kolaborasi_buku) => match ($transaksi_kolaborasi_buku->status) {
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
                    ->hidden(function (transaksi_kolaborasi_buku $transaksi) {
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
                            ->default(fn (transaksi_kolaborasi_buku $transaksi) => $transaksi->foto_bukti_bayar)
                            ->openable()
                            ->downloadable()
                            ->image()
                            ->imagePreviewHeight('500')
                            ->panelAspectRatio('1:1')
                            ->disabled(),
                    ])
                    ->action(
                        function (transaksi_kolaborasi_buku $transaksi): void {
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
                    ->hidden(function (transaksi_kolaborasi_buku $transaksi) {
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
                            ->default(fn (transaksi_kolaborasi_buku $transaksi) => $transaksi->foto_bukti_bayar)
                            ->openable()
                            ->downloadable()
                            ->image()
                            ->imagePreviewHeight('500')
                            ->panelAspectRatio('1:1')
                            ->disabled(),
                    ])
                    ->action(
                        function (transaksi_kolaborasi_buku $transaksi): void {
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
            'index' => Pages\ListTransaksiKolaborasiBukus::route('/'),
            // 'create' => Pages\CreateTransaksiKolaborasiBuku::route('/create'),
            // 'edit' => Pages\EditTransaksiKolaborasiBuku::route('/{record}/edit'),
        ];
    }
}
