<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KeranjangResource\Pages;
use App\Filament\Resources\KeranjangResource\RelationManagers;
use App\Models\Keranjang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KeranjangResource extends Resource
{
    protected static ?string $model = Keranjang::class;

    protected static ?string $navigationIcon = 'heroicon-s-shopping-cart';

    protected static ?string $navigationLabel = 'Keranjang';

    protected static ?string $label = 'Keranjang';

    protected static ?string $slug = 'keranjang';

    protected static ?string $title = 'Keranjang';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup(
                Tables\Grouping\Group::make('user.nama_lengkap')
                    ->collapsible()
                    ->getDescriptionFromRecordUsing(function (Keranjang $record) {
                        // count total buku_dijual in keranjang
                        $data = Keranjang::with('buku_dijual')->where('user_id', $record->user_id)->get();

                        return 'Total Buku : ' . count($data->toArray()) . ' Buku';
                    })
                    ->label('Nama User ')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('buku_dijual.cover_buku')
                    ->size(80)
                    ->label('Cover Buku'),
                Tables\Columns\TextColumn::make('buku_dijual.judul')
                    ->label('Judul Buku')
                    ->searchable(),
                Tables\Columns\TextColumn::make('buku_dijual.harga')
                    ->label('Harga Buku')
                    ->money('idr')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->money('idr')->label('Total Harga'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.nama_lengkap')
                    ->label('Nama')
                    ->toggleable(false)
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
            'index' => Pages\ListKeranjangs::route('/'),
            // 'create' => Pages\CreateKeranjang::route('/create'),
            // 'edit' => Pages\EditKeranjang::route('/{record}/edit'),
        ];
    }
}
