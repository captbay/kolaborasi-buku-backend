<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BukuLunasUserResource\Pages;
use App\Filament\Resources\BukuLunasUserResource\RelationManagers;
use App\Models\buku_lunas_user;
use App\Models\BukuLunasUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BukuLunasUserResource extends Resource
{
    protected static ?string $model = buku_lunas_user::class;

    protected static ?string $navigationLabel = 'Buku User Lunas';

    protected static ?string $label = 'Buku User Lunas';

    protected static ?string $slug = 'buku-user-lunas';

    protected static ?string $title = 'Buku User Lunas';

    // protected static ?int $navigationSort = 0;

    protected static ?string $navigationIcon = 'heroicon-s-bookmark-square';

    // navigation groups

    protected static ?string $navigationGroup = 'Buku Dijual';

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup(
                Tables\Grouping\Group::make('user.nama_lengkap')
                    ->collapsible()
                    ->getDescriptionFromRecordUsing(function (buku_lunas_user $record) {
                        // count total buku_dijual in buku_lunas_user
                        $data = buku_lunas_user::with('buku_dijual')->where('user_id', $record->user_id)->get();

                        return 'Total Buku : ' . count($data->toArray()) . ' Buku';
                    })
                    ->label('Nama User ')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('buku_dijual.cover_buku')
                    ->size(80)
                    ->label('Cover Buku'),
                Tables\Columns\TextColumn::make('buku_dijual.judul')
                    ->wrap()
                    ->label('Judul Buku')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.nama_lengkap')
                    ->label('Nama')
                    ->toggleable(isToggledHiddenByDefault: true)
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
                //
            ])
            ->recordUrl(false)
            ->bulkActions([
                //
            ]);
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
            'index' => Pages\ListBukuLunasUsers::route('/'),
            // 'create' => Pages\CreateBukuLunasUser::route('/create'),
            // 'edit' => Pages\EditBukuLunasUser::route('/{record}/edit'),
        ];
    }
}
