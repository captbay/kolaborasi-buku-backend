<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConfigWebResource\Pages;
use App\Filament\Resources\ConfigWebResource\RelationManagers;
use App\Models\config_web;
use App\Models\ConfigWeb;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConfigWebResource extends Resource
{
    protected static ?string $model = config_web::class;

    protected static ?string $navigationIcon = 'heroicon-s-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Config';

    protected static ?string $label = 'Config';

    protected static ?string $slug = 'config';

    protected static ?string $title = 'Config';

    protected static ?int $navigationSort = 3;

    // navigation groups

    protected static ?string $navigationGroup = 'Setting Web Customer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->columnSpan('full')
                    ->maxLength(255)
                    ->helperText('*Key harus unik dan berhati hati dalam penamaan karena akan terintegrasi dengan website client')
                    ->unique(config_web::class, 'key', ignoreRecord: true)
                    ->hiddenOn('edit'),

                Forms\Components\Select::make('tipe')
                    ->columnSpan('full')
                    ->options([
                        'IMAGE' => 'Image',
                        'TEXT' => 'Text',
                    ])
                    ->live(onBlur: true)
                    ->hiddenOn('edit')
                    ->required(),

                Forms\Components\Section::make('Value')
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->required()
                            ->columnSpan('full')
                            ->maxLength(255)
                            ->hidden(fn (Forms\Get $get) => $get('tipe') !== 'TEXT'),

                        Forms\Components\FileUpload::make('value')
                            ->label('image')
                            ->required()
                            ->openable()
                            ->image()
                            ->imageEditor()
                            ->hidden(fn (Forms\Get $get) => $get('tipe') !== 'IMAGE')
                            ->directory('config_web'),
                    ])->description('Pilih dahulu tipe'),

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
                Tables\Columns\TextColumn::make('key')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipe')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
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
                    // Tables\Actions\DeleteAction::make(),
                ])->iconButton()
            ])
            ->recordUrl(false)
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListConfigWebs::route('/'),
            'create' => Pages\CreateConfigWeb::route('/create'),
            'edit' => Pages\EditConfigWeb::route('/{record}/edit'),
        ];
    }
}
