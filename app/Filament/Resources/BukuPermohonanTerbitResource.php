<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BukuPermohonanTerbitResource\Pages;
use App\Filament\Resources\BukuPermohonanTerbitResource\RelationManagers;
use App\Models\buku_permohonan_terbit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BukuPermohonanTerbitResource extends Resource
{
    protected static ?string $model = buku_permohonan_terbit::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square-stack';

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

                                Forms\Components\MarkdownEditor::make('deskripsi')
                                    ->required(),
                            ]),

                        Forms\Components\Section::make('File')
                            ->schema([
                                Forms\Components\FileUpload::make('file_buku')
                                    ->label('Upload File Buku PDF (final version)')
                                    ->required()
                                    ->openable()
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->directory('buku_permohonan_terbit'),
                            ]),


                    ])
                    ->columnSpan(['lg' => 2]),


                Forms\Components\Group::make()
                    ->schema([

                        Forms\Components\Section::make('Member')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->relationship(
                                        'user',
                                        'nama_lengkap',
                                        fn (Builder $query) => $query->whereNot('id', auth()->user()->id)
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->label(false)
                                    ->required(),
                            ]),

                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label(false)
                                    ->searchable()
                                    ->default('REVIEW')
                                    ->options([
                                        'REVIEW' => 'Review',
                                        'REVISI' => 'Revisi',
                                        'ACCEPTED' => 'Accepted',
                                        'REJECTED' => 'Rejected',
                                    ])
                                    ->live()
                                    ->required(),
                            ]),

                        Forms\Components\Section::make('Persen Bagi Hasil')
                            ->schema([
                                Forms\Components\TextInput::make('persen_bagi_hasil')
                                    ->numeric()
                                    ->label(false)
                                    ->helperText('Dalam persen (%)')
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
                Tables\Columns\TextColumn::make('user.nama_depan')
                    ->label('Nama Lengkap')
                    ->formatStateUsing(function ($state, buku_permohonan_terbit $buku_permohonan_terbit) {
                        return $buku_permohonan_terbit->user->nama_depan . ' ' . $buku_permohonan_terbit->user->nama_belakang;
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(
                        fn (buku_permohonan_terbit $buku_permohonan_terbit) => match ($buku_permohonan_terbit->status) {
                            'ACCEPTED' => 'success',
                            'REVIEW' => 'info',
                            'REVISI' => 'warning',
                            'REJECTED' => 'danger',
                        }
                    )
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
            'index' => Pages\ListBukuPermohonanTerbits::route('/'),
            'create' => Pages\CreateBukuPermohonanTerbit::route('/create'),
            'edit' => Pages\EditBukuPermohonanTerbit::route('/{record}/edit'),
        ];
    }
}
