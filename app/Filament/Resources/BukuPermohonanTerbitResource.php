<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BukuPermohonanTerbitResource\Pages;
use App\Models\buku_permohonan_terbit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BukuPermohonanTerbitResource extends Resource
{
    protected static ?string $model = buku_permohonan_terbit::class;

    protected static ?string $navigationIcon = 'heroicon-s-arrow-up-on-square-stack';

    protected static ?string $navigationLabel = 'Buku Permohonan Terbit';

    protected static ?string $label = 'Buku Permohonan Terbit';

    protected static ?string $slug = 'buku-permohonan-terbit';

    protected static ?string $title = 'Buku Permohonan Terbit';

    protected static ?int $navigationSort = 2;

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
                                Forms\Components\TextInput::make('judul')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(buku_permohonan_terbit::class, 'judul', ignoreRecord: true),

                                Forms\Components\Textarea::make('deskripsi')
                                    ->required(),
                            ]),

                        Forms\Components\FileUpload::make('cover_buku')
                            ->required()
                            ->openable()
                            ->image()
                            ->imageEditor()
                            ->helperText('Gambar direkomendasikan berukuran 192 px x 192px')
                            ->directory('cover_buku_permohonan_terbit'),

                        Forms\Components\FileUpload::make('file_buku')
                            ->label('Upload File Buku PDF (final version)')
                            ->required()
                            ->openable()
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('buku_permohonan_terbit'),

                        Forms\Components\FileUpload::make('file_mou')
                            ->label('Upload file MOU')
                            ->openable()
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('mou_buku_permohonan_penerbitan'),

                    ])
                    ->columnSpan(['lg' => 2]),


                Forms\Components\Group::make()
                    ->schema([

                        Forms\Components\Section::make('Detail')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->relationship(
                                        'user',
                                        'nama_lengkap',
                                        fn (Builder $query) => $query->whereNot('id', auth()->user()->id)
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->label('Nama Member Yang Mengajukan')
                                    ->required(),

                                Forms\Components\TextInput::make('isbn')
                                    ->label('ISBN')
                                    ->required()
                                    ->unique(buku_permohonan_terbit::class, 'isbn', ignoreRecord: true),
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
                Tables\Columns\TextColumn::make('user.nama_lengkap')
                    ->label('Nama Lengkap')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul Buku')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->wrap()
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
                Tables\Actions\ViewAction::make()->slideOver(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(function (buku_permohonan_terbit $buku_permohonan_terbit) {
                        if ($buku_permohonan_terbit->dijual != 1) {
                            return true;
                        }

                        return false;
                    }),
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
            'index' => Pages\ListBukuPermohonanTerbits::route('/'),
            'create' => Pages\CreateBukuPermohonanTerbit::route('/create'),
            'edit' => Pages\EditBukuPermohonanTerbit::route('/{record}/edit'),
        ];
    }
}
