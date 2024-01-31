<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BukuKolaborasiResource\Pages;
use App\Filament\Resources\BukuKolaborasiResource\RelationManagers;
use App\Models\buku_kolaborasi;
use App\Models\BukuKolaborasi;
use App\Models\kategori;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\penulis;
use Illuminate\Support\Str;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Facades\Storage;

class BukuKolaborasiResource extends Resource
{
    protected static ?string $model = buku_kolaborasi::class;

    protected static ?string $navigationLabel = 'Buku Kolaborasi';

    protected static ?string $label = 'Buku Kolaborasi';

    protected static ?string $slug = 'buku-kolaborasi';

    protected static ?string $title = 'Buku Kolaborasi';

    // protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-s-book-open';

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
                                Forms\Components\TextInput::make('judul')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation !== 'create') {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));
                                    })
                                    ->unique(buku_kolaborasi::class, 'judul', ignoreRecord: true),

                                Forms\Components\TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(buku_kolaborasi::class, 'slug', ignoreRecord: true),

                                Forms\Components\MarkdownEditor::make('deskripsi')
                                    ->columnSpan('full')
                                    ->required(),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Bab Buku')
                            ->schema([
                                Repeater::make('bab_buku_kolaborasi')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('no_bab')
                                            ->label('Bab')
                                            // ->default(fn (Get $get) => $get('../../jumlah_bab') + 1)
                                            ->afterStateHydrated(function (Forms\Components\TextInput $component, ?string $state, Get $get) {
                                                // if the value is empty in the database, set a default value, if not, just continue with the default component hydration
                                                if (!$state) {
                                                    $component->state($get('../../jumlah_bab') + 1);
                                                }
                                            })
                                            ->disabled()
                                            ->dehydrated()
                                            ->numeric()
                                            ->required(),

                                        Forms\Components\TextInput::make('durasi_pembuatan')
                                            ->label('Durasi Hari Pembuatan')
                                            ->columnSpan(3)
                                            ->numeric()
                                            ->required(),

                                        Forms\Components\TextInput::make('harga')
                                            ->label('Harga')
                                            ->columnSpan(3)
                                            ->numeric()
                                            ->required(),

                                        Forms\Components\TextInput::make('judul')
                                            ->label('Judul Bab')
                                            ->columnSpan('full')
                                            ->required(),

                                        Forms\Components\Textarea::make('deskripsi')
                                            ->label('Deskripsi Bab')
                                            ->columnSpan('full')
                                            ->required(),

                                        Forms\Components\Toggle::make('active_flag')
                                            ->label('Dipublish atau tidak')
                                            ->helperText('Jika tidak aktif maka bab ini akan disembunyikan pada tampilan kolaborasi buku')
                                            ->columnSpan('full')
                                            ->default(0),
                                    ])
                                    ->defaultItems(0)
                                    ->hiddenLabel()
                                    ->columns(7)
                                    ->minItems(1)
                                    ->live(onBlur: true)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $set('jumlah_bab', count($state));
                                    })
                                    ->required(),
                            ])
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('File')
                            ->schema([
                                Forms\Components\FileUpload::make('cover_buku')
                                    ->required()
                                    ->openable()
                                    ->image()
                                    ->directory('cover_buku_kolaborasi'),

                                Forms\Components\FileUpload::make('file_sertifikasi')
                                    ->label('Upload file sertifikat kalau sudah ada')
                                    ->openable()
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->directory('sertifikasi_buku_kolaborasi'),
                            ]),

                        Forms\Components\Section::make('Kategori')
                            ->schema([
                                Forms\Components\Select::make('kategori_id')
                                    ->relationship('kategori', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->label(false)
                                    ->required(),
                            ]),

                        Forms\Components\Section::make('Jumlah Bab Buku')
                            ->schema([
                                Forms\Components\TextInput::make('jumlah_bab')
                                    ->label(false)
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->reactive()
                                    ->live(onBlur: true)
                                    ->required(),
                            ]),

                        Forms\Components\Section::make('Bahasa')
                            ->schema([
                                Forms\Components\Select::make('bahasa')
                                    ->label(false)
                                    ->searchable()
                                    ->options([
                                        'Indonesia' => 'Indonesia',
                                        'Inggris' => 'Inggris',
                                        'Belanda' => 'Belanda',
                                        'Prancis' => 'Prancis',
                                    ])
                                    ->live()
                                    ->required(),
                            ]),
                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Toggle::make('active_flag')
                                    ->label('Dipublish atau tidak')
                                    ->helperText('Jika tidak aktif maka buku ini akan disembunyikan pada tampilan penjualan buku')
                                    ->default(0),
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
                Tables\Columns\ImageColumn::make('cover_buku')
                    ->size(80)
                    ->label('Cover Buku'),
                Tables\Columns\TextColumn::make('judul')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori.nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bahasa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_bab')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('active_flag')
                    ->badge()
                    ->formatStateUsing(function ($state, buku_kolaborasi $buku_kolaborasi) {
                        return match ($buku_kolaborasi->active_flag) {
                            1 => 'Iya',
                            0 => 'Tidak',
                        };
                    })
                    ->color(
                        fn (buku_kolaborasi $buku_kolaborasi) => match ($buku_kolaborasi->active_flag) {
                            1 => 'success',
                            0 => 'warning',
                        }
                    )
                    ->label('Dipublish')
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
                Tables\Filters\TernaryFilter::make('active_flag')
                    ->label('Dipublish atau tidak'),
                Tables\Filters\SelectFilter::make('kategori_id')
                    ->label('kategori')
                    ->placeholder('Semua Kategori')
                    ->options(
                        kategori::all()->pluck('nama', 'id')
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->iconButton()
            ])
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
            'index' => Pages\ListBukuKolaborasis::route('/'),
            'create' => Pages\CreateBukuKolaborasi::route('/create'),
            'edit' => Pages\EditBukuKolaborasi::route('/{record}/edit'),
        ];
    }
}
