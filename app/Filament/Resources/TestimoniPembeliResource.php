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

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             //
    //         ]);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('buku_dijual.cover_buku')
                    ->size(80)
                    ->label('Cover Buku'),
                Tables\Columns\TextColumn::make('user.nama_depan')
                    ->label('Nama Lengkap')
                    ->formatStateUsing(function ($state, testimoni_pembeli $testi) {
                        return $testi->user->nama_lengkap;
                    })
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
                Tables\Columns\TextColumn::make('rating')
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
                // Tables\Actions\ActionGroup::make([
                // Tables\Actions\ViewAction::make()->slideOver(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                // ])

                Tables\Actions\Action::make('Hide')
                    ->hidden(function (testimoni_pembeli $testimoni_pembeli, array $data) {
                        if ($testimoni_pembeli->active_flag == 0) {
                            return true;
                        }

                        return false;
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Nonaktifkan Testimoni Pembeli')
                    ->modalDescription('Apakah anda yakin ingin menonaktifkan testimoni pembeli ini?')
                    ->modalSubmitActionLabel('iya, nonaktifkan')
                    ->color('danger')
                    ->modalIcon('heroicon-s-chat-bubble-left-ellipsis')
                    ->icon('heroicon-s-x-circle')
                    ->modalIconColor('danger')
                    ->action(
                        function (testimoni_pembeli $testimoni_pembeli, array $data): void {
                            if ($testimoni_pembeli->active_flag == 0) {
                                Notification::make()
                                    ->danger()
                                    ->title('Testimoni pembeli sudah dinonaktifkan')
                                    ->send();

                                return;
                            }

                            //  nonaktifkan testimoni pembeli
                            $testimoni_pembeli->update([
                                'active_flag' => 0,
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Testimoni pembeli berhasil dinonaktifkan')
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
