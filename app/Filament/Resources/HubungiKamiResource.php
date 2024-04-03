<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HubungiKamiResource\Pages;
use App\Filament\Resources\HubungiKamiResource\RelationManagers;
use App\Models\hubungi_kami;
use App\Models\HubungiKami;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HubungiKamiResource extends Resource
{
    protected static ?string $model = hubungi_kami::class;

    protected static ?string $navigationIcon = 'heroicon-s-chat-bubble-bottom-center-text';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'pesan-user';

    protected static ?string $navigationLabel = '#HubungiKami';
    protected static ?string $label = 'Pesan User';
    protected static ?string $title = 'Pesan User';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->sortable()
                    ->wrap()
                    ->label('Nama User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->wrap()
                    ->label('Nama User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subjek')
                    ->sortable()
                    ->wrap()
                    ->label('Nama User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pesan')
                    ->wrap()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->recordUrl(false)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHubungiKamis::route('/'),
            // 'create' => Pages\CreateHubungiKami::route('/create'),
            // 'edit' => Pages\EditHubungiKami::route('/{record}/edit'),
        ];
    }
}
