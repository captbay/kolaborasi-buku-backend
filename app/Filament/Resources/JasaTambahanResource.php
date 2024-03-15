<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JasaTambahanResource\Pages;
use App\Models\jasa_tambahan;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;

class JasaTambahanResource extends Resource
{
    protected static ?string $model = jasa_tambahan::class;

    protected static ?string $navigationLabel = 'Jasa Tambahan';

    protected static ?string $label = 'Jasa Tambahan';

    protected static ?string $slug = 'jasa-tambahan';

    protected static ?string $title = 'Jasa Tambahan';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-s-folder-plus';

    protected static ?string $navigationGroup = 'Buku Permohonan Terbit';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255)
                    ->unique(jasa_tambahan::class, 'nama', ignoreRecord: true),

                Forms\Components\TextInput::make('harga')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Jasa')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga')
                    ->searchable()
                    ->money('idr')
                    ->sortable(),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->slideOver(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->iconButton()
            ])
            ->recordUrl(false)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListJasaTambahans::route('/'),
            'create' => Pages\CreateJasaTambahan::route('/create'),
            'edit' => Pages\EditJasaTambahan::route('/{record}/edit'),
        ];
    }
}
