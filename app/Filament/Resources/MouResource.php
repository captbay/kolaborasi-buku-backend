<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MouResource\Pages;
use App\Filament\Resources\MouResource\RelationManagers;
use App\Models\Mou;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MouResource extends Resource
{
    protected static ?string $model = Mou::class;

    protected static ?string $navigationIcon = 'heroicon-s-document';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'mou';

    protected static ?string $navigationLabel = 'MOU';
    protected static ?string $label = 'MOU';
    protected static ?string $title = 'MOU';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->unique(Mou::class, 'nama', ignoreRecord: true)
                    ->columnSpan('full')
                    ->required(),

                Forms\Components\Select::make('kategori')
                    ->searchable()
                    ->options([
                        'kolaborasi' => 'Kolaborasi',
                        'paket penerbitan' => 'Paket Penerbitan',
                    ])
                    ->live()
                    ->columnSpan('full')
                    ->required(),

                Forms\Components\FileUpload::make('file_mou')
                    ->label('Upload MOU')
                    ->columnSpan('full')
                    ->required()
                    ->openable()
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory('mou_file'),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('active_flag')
                            ->label('Dipublish atau tidak')
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListMous::route('/'),
            'create' => Pages\CreateMou::route('/create'),
            'edit' => Pages\EditMou::route('/{record}/edit'),
        ];
    }
}
