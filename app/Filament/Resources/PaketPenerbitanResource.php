<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaketPenerbitanResource\Pages;
use App\Filament\Resources\PaketPenerbitanResource\RelationManagers;
use App\Models\paket_penerbitan;
use App\Models\PaketPenerbitan;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaketPenerbitanResource extends Resource
{
    protected static ?string $model = paket_penerbitan::class;

    protected static ?string $navigationIcon = 'heroicon-s-banknotes';

    protected static ?string $navigationLabel = 'Paket Penerbitan';

    protected static ?string $label = 'Paket Penerbitan';

    protected static ?string $slug = 'paket-penerbitan';

    protected static ?string $title = 'Paket Penerbitan';

    protected static ?int $navigationSort = 1;

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
                                Forms\Components\TextInput::make('nama')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(paket_penerbitan::class, 'nama', ignoreRecord: true),

                                Forms\Components\TextInput::make('harga')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required(),

                                Forms\Components\MarkdownEditor::make('deskripsi')
                                    ->columnSpan('full')
                                    ->required(),
                            ])->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),


                Forms\Components\Group::make()
                    ->schema([

                        Forms\Components\Section::make('Tanggal Waktu Mulai')
                            ->schema([
                                Forms\Components\DateTimePicker::make('waktu_mulai')
                                    ->label(false)
                                    ->after(Carbon::now())
                                    ->before('waktu_selesai')
                                    ->required(),
                            ]),

                        Forms\Components\Section::make('Tanggal Waktu Selesai')
                            ->schema([
                                Forms\Components\DateTimePicker::make('waktu_selesai')
                                    ->label(false)
                                    ->after(Carbon::parse('tomorrow'))
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),

            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga')
                    ->searchable()
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('waktu_mulai')
                    ->label('Tanggal Waktu Mulai')
                    ->formatStateUsing(function (paket_penerbitan $paket) {
                        return Carbon::parse($paket->waktu_mulai)->format('d F Y // H:i');
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('waktu_selesai')
                    ->label('Tanggal Waktu Selesai')
                    ->formatStateUsing(function (paket_penerbitan $paket) {
                        return Carbon::parse($paket->datetime_deadline)->format('d F Y // H:i');
                    })
                    ->searchable()
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
            'index' => Pages\ListPaketPenerbitans::route('/'),
            'create' => Pages\CreatePaketPenerbitan::route('/create'),
            'edit' => Pages\EditPaketPenerbitan::route('/{record}/edit'),
        ];
    }
}
