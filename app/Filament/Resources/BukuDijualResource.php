<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BukuDijualResource\Pages;
use App\Filament\Resources\BukuDijualResource\RelationManagers;
use App\Models\buku_dijual;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\CheckboxColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BukuDijualResource extends Resource
{
    protected static ?string $model = buku_dijual::class;

    protected static ?string $navigationLabel = 'Buku Dijual';

    protected static ?string $label = 'Buku Dijual';

    protected static ?string $slug = 'buku-dijual';

    protected static ?string $title = 'Buku Dijual';

    // protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-s-book-open';

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
                                    ->unique(buku_dijual::class, 'judul', ignoreRecord: true),

                                Forms\Components\TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(buku_dijual::class, 'slug', ignoreRecord: true),

                                Forms\Components\MarkdownEditor::make('deskripsi')
                                    ->columnSpan('full')
                                    ->required(),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('File')
                            ->schema([
                                // SpatieMediaLibraryFileUpload::make('media')
                                //     ->collection('cover-buku')
                                //     ->multiple()
                                //     ->maxFiles(5)
                                //     ->hiddenLabel(),
                                Forms\Components\FileUpload::make('cover_buku')
                                    ->required()
                                    ->image()
                                    ->directory('cover_buku'),
                                Forms\Components\FileUpload::make('storage_buku_dijual')
                                    ->relationship('storage_buku_dijual', 'file_name')
                                    ->multiple()
                                    ->required()
                                    ->image()
                                    ->directory('storage_buku'),
                            ]),

                        Forms\Components\Section::make('Harga Buku')
                            ->schema([
                                Forms\Components\TextInput::make('harga')
                                    ->numeric()
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->required(),

                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Detail Buku')
                            ->schema([
                                Forms\Components\TextInput::make('penerbit')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\DatePicker::make('tanggal_terbit')
                                    ->maxDate('today')
                                    ->required(),
                                Forms\Components\TextInput::make('jumlah_halaman')
                                    ->label('Jumlah Halaman')
                                    ->numeric()
                                    ->rules(['integer', 'min:0'])
                                    ->required(),

                                Forms\Components\Select::make('bahasa')
                                    ->searchable()
                                    ->options([
                                        'Indonesia' => 'Indonesia',
                                        'Inggris' => 'Inggris',
                                        'Belanda' => 'Belanda',
                                        'Prancis' => 'Prancis',
                                    ])
                                    ->live()
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Select::make('penulis_id')
                            ->relationship('penulis', 'nama')
                            ->multiple()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama')
                                    ->required(),
                            ]),

                        Forms\Components\Section::make('Kategori')
                            ->schema([
                                Forms\Components\Select::make('kategori_id')
                                    ->relationship('kategori', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),

                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Toggle::make('active_flag')
                                    ->label('Ditampilkan atau tidak')
                                    ->helperText('Jika tidak aktif maka buku ini akan disembunyikan pada tampilan penjualan buku')
                                    ->default(true),
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
                    ->label('Cover Buku'),
                Tables\Columns\TextColumn::make('judul')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori.nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bahasa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bukudijual_penulis_pivot.penulis.nama')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                CheckboxColumn::make('active_flag')
                    ->label('Ditampilkan'),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListBukuDijuals::route('/'),
            'create' => Pages\CreateBukuDijual::route('/create'),
            'edit' => Pages\EditBukuDijual::route('/{record}/edit'),
        ];
    }
}
