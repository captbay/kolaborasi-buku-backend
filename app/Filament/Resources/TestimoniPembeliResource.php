<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimoniPembeliResource\Pages;
use App\Filament\Resources\TestimoniPembeliResource\RelationManagers;
use App\Models\testimoni_pembeli;
use App\Models\TestimoniPembeli;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use IbrahimBougaoua\FilamentRatingStar\Columns\RatingStarColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestimoniPembeliResource extends Resource
{
    protected static ?string $model = testimoni_pembeli::class;

    protected static ?string $navigationIcon = 'heroicon-s-chat-bubble-bottom-center-text';

    protected static ?string $navigationLabel = 'Testimoni Pembeli';

    protected static ?string $label = 'Testimoni Pembeli';

    protected static ?string $slug = 'testimoni-pembeli';

    protected static ?string $title = 'Testimoni Pembeli';

    // protected static ?int $navigationSort = 3;

    // navigation groups

    protected static ?string $navigationGroup = 'Buku Dijual';


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('buku_dijual.cover_buku')
                    ->size(80)
                    ->label('Cover Buku'),
                Tables\Columns\TextColumn::make('user.nama_lengkap')
                    ->label('Nama Lengkap')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('buku_dijual.judul')
                    ->sortable()
                    ->wrap()
                    ->label('Judul Buku')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ulasan')
                    ->wrap()
                    ->searchable(),
                RatingStarColumn::make('rating')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('active_flag')
                    ->label('Dipublish')
                    ->boolean()
                    ->summarize(Tables\Columns\Summarizers\Count::make()->icons()),
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
                Tables\Filters\TernaryFilter::make('active_flag')
                    ->label('Dipublish atau tidak'),
            ])
            ->actions([
                Tables\Actions\Action::make('Sembunyi')
                    ->hidden(function (testimoni_pembeli $testimoni_pembeli, array $data) {
                        if ($testimoni_pembeli->active_flag == 0) {
                            return true;
                        }

                        return false;
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Sembunyikan Testimoni Pembeli')
                    ->modalDescription('Apakah anda yakin ingin menonaktifkan testimoni pembeli ini?')
                    ->modalSubmitActionLabel('iya, sembunyikan')
                    ->color('danger')
                    ->modalIcon('heroicon-s-chat-bubble-left-ellipsis')
                    ->icon('heroicon-s-x-circle')
                    ->modalIconColor('danger')
                    ->action(
                        function (testimoni_pembeli $testimoni_pembeli, array $data): void {
                            if ($testimoni_pembeli->active_flag == 0) {
                                Notification::make()
                                    ->danger()
                                    ->title('Testimoni pembeli sudah disembunyikan')
                                    ->send();

                                return;
                            }

                            //  nonaktifkan testimoni pembeli
                            $testimoni_pembeli->update([
                                'active_flag' => 0,
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Testimoni pembeli berhasil disembunyikan')
                                ->send();

                            return;
                        }
                    ),

                Tables\Actions\Action::make('Tampil')
                    ->hidden(function (testimoni_pembeli $testimoni_pembeli, array $data) {
                        if ($testimoni_pembeli->active_flag == 1) {
                            return true;
                        }

                        return false;
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Tampilkan Testimoni Pembeli')
                    ->modalDescription('Apakah anda yakin ingin mengaktifkan testimoni pembeli ini?')
                    ->modalSubmitActionLabel('iya, tampilkan')
                    ->color('success')
                    ->modalIcon('heroicon-s-chat-bubble-left-ellipsis')
                    ->icon('heroicon-s-check-circle')
                    ->modalIconColor('success')
                    ->action(
                        function (testimoni_pembeli $testimoni_pembeli, array $data): void {
                            if ($testimoni_pembeli->active_flag == 1) {
                                Notification::make()
                                    ->danger()
                                    ->title('Testimoni pembeli sudah ditampilkan')
                                    ->send();

                                return;
                            }

                            //  nonaktifkan testimoni pembeli
                            $testimoni_pembeli->update([
                                'active_flag' => 1,
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Testimoni pembeli berhasil ditampilkan')
                                ->send();

                            return;
                        }
                    ),
            ])
            ->recordUrl(false)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // public static function getRelations(): array
    // {
    //     return [
    //         //
    //     ];
    // }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimoniPembelis::route('/'),
            // 'create' => Pages\CreateTestimoniPembeli::route('/create'),
            // 'edit' => Pages\EditTestimoniPembeli::route('/{record}/edit'),
        ];
    }
}
