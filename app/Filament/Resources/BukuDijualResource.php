<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BukuDijualResource\Pages;
use App\Filament\Resources\BukuDijualResource\RelationManagers;
use App\Models\buku_dijual;
use App\Models\kategori;
use App\Models\penulis;
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
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Facades\Storage;

class BukuDijualResource extends Resource
{
    protected static ?string $model = buku_dijual::class;

    protected static ?string $navigationLabel = 'Buku Dijual';

    protected static ?string $label = 'Buku Dijual';

    protected static ?string $slug = 'buku-dijual';

    protected static ?string $title = 'Buku Dijual';

    // protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-s-book-open';

    // navigation groups

    protected static ?string $navigationGroup = 'Buku Dijual';

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
                                Forms\Components\FileUpload::make('cover_buku')
                                    ->required()
                                    ->openable()
                                    ->image()
                                    ->imageEditor()
                                    ->directory('cover_buku_dijual'),

                                Forms\Components\FileUpload::make('file_buku')
                                    ->label('Upload File Buku PDF (final version)')
                                    ->required()
                                    ->openable()
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->storeFileNamesIn('nama_file_buku')
                                    ->directory('buku_final_storage'),

                                Repeater::make('preview_buku')
                                    ->relationship('storage_buku_dijual')
                                    ->label('File Preview Buku')
                                    ->schema([
                                        Forms\Components\FileUpload::make('nama_generate')
                                            ->label('Upload Gambar untuk preview buku')
                                            ->required()
                                            ->openable()
                                            ->image()
                                            ->imageEditor()
                                            ->storeFileNamesIn('nama_file')
                                            ->directory('buku_preview_storage'),
                                    ])
                                    ->defaultItems(1)
                                    ->required(),
                            ]),

                        Forms\Components\Section::make('Detail Buku')
                            ->schema([
                                Forms\Components\TextInput::make('penerbit')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\DatePicker::make('tanggal_terbit')
                                    ->native(false)
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
                                    ])
                                    ->live()
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Harga Buku')
                            ->schema([
                                Forms\Components\TextInput::make('harga')
                                    ->numeric()
                                    ->label(false)
                                    ->required(),
                            ]),

                        Forms\Components\Section::make('Penulis')
                            ->schema([
                                Forms\Components\Select::make('penulis_id')
                                    ->relationship('penulis', 'nama')
                                    ->multiple()
                                    ->preload()
                                    ->label(false)
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nama')
                                            ->unique(penulis::class, 'nama', ignoreRecord: true)
                                            ->required(),
                                    ])
                                    ->createOptionAction(function (Action $action) {
                                        return $action
                                            ->modalHeading('Buat Data Penulis')
                                            ->modalSubmitActionLabel('Buat penulis')
                                            ->modalWidth('lg');
                                    }),
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
                Tables\Columns\TextColumn::make('harga')
                    ->searchable()
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori.nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bahasa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bukudijual_penulis_pivot.penulis.nama')
                    ->bulleted()
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
                TernaryFilter::make('active_flag')
                    ->label('Dipublish atau tidak'),
                SelectFilter::make('kategori_id')
                    ->label('kategori')
                    ->placeholder('Semua Kategori')
                    ->options(
                        kategori::all()->pluck('nama', 'id')
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->slideOver(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->before(function ($record) {
                            // if not have storage_buku_dijual
                            if (!$record->storage_buku_dijual()->exists()) {
                                return;
                            }

                            // get all the name_generate
                            $name_generate = $record->storage_buku_dijual()->get('nama_generate');
                            // foreach name_generate
                            foreach ($name_generate as $name) {
                                // delete the file
                                Storage::disk('public')->delete($name['nama_generate']);
                            }
                        }),
                ])->iconButton()

            ])
            ->recordUrl(false)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($record) {
                            // if not have storage_buku_dijual
                            if (!$record->storage_buku_dijual()->exists()) {
                                return;
                            }

                            // get all the name_generate
                            $name_generate = $record->storage_buku_dijual()->get('nama_generate');
                            // foreach name_generate
                            foreach ($name_generate as $name) {
                                // delete the file
                                Storage::disk('public')->delete($name['nama_generate']);
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\StorageBukuDijualRelationManager::class,
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
