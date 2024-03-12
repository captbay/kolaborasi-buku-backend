<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BukuPermohonanTerbitResource\Pages;
use App\Filament\Resources\BukuPermohonanTerbitResource\RelationManagers;
use App\Models\buku_dijual;
use App\Models\buku_permohonan_terbit;
use App\Models\kategori;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BukuPermohonanTerbitResource extends Resource
{
    protected static ?string $model = buku_permohonan_terbit::class;

    protected static ?string $navigationIcon = 'heroicon-s-arrow-up-on-square-stack';

    protected static ?string $navigationLabel = 'Buku Permohonan Terbit';

    protected static ?string $label = 'Buku Permohonan Terbit';

    protected static ?string $slug = 'buku-permohonan-terbit';

    protected static ?string $title = 'Buku Permohonan Terbit';

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
                                Forms\Components\TextInput::make('judul')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(buku_permohonan_terbit::class, 'judul', ignoreRecord: true),

                                Forms\Components\MarkdownEditor::make('deskripsi')
                                    ->required(),
                            ]),

                        Forms\Components\FileUpload::make('cover_buku')
                            ->required()
                            ->openable()
                            ->image()
                            ->imageEditor()
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
                                        'ACCEPTED' => 'Siap Jual',
                                        'REJECTED' => 'Ditolak',
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
                    Tables\Actions\Action::make('Terbitkan')
                        ->slideOver()
                        ->hidden(function (buku_permohonan_terbit $buku_permohonan_terbit) {
                            if ($buku_permohonan_terbit->status != 'ACCEPTED') {
                                return true;
                            }

                            return false;
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Terbitkan Buku')
                        ->modalDescription('Apakah anda yakin ingin menerbitkan buku ini? Pastikan file buku sudah benar')
                        ->modalSubmitActionLabel('iya, terbitkan')
                        ->color('success')
                        ->modalIcon('heroicon-o-book-open')
                        ->icon('heroicon-o-book-open')
                        ->modalIconColor('success')
                        ->form([
                            Forms\Components\TextInput::make('harga')
                                ->numeric()
                                ->label('Harga Buku')
                                ->minValue(1)
                                ->required(),

                            Forms\Components\Select::make('kategori_id')
                                ->label('Kategori')
                                ->options(kategori::all()->pluck('nama', 'id'))
                                ->searchable()
                                ->preload()
                                ->required(),

                            Forms\Components\Select::make('bahasa')
                                ->searchable()
                                ->options([
                                    'Indonesia' => 'Indonesia',
                                    'Inggris' => 'Inggris',
                                ])
                                ->required(),

                            Forms\Components\FileUpload::make('cover_buku')
                                ->required()
                                ->openable()
                                ->image()
                                ->imageEditor()
                                ->directory('cover_buku_dijual'),

                            Forms\Components\TextInput::make('isbn')
                                ->label('ISBN')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\Repeater::make('preview_buku')
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
                        ])
                        ->action(
                            function (buku_permohonan_terbit $buku_permohonan_terbit, array $data): void {
                                if ($buku_permohonan_terbit->dijual == 1) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Buku sudah diterbitkan')
                                        ->send();

                                    return;
                                }

                                // split name of $buku_permohonan_terbit->file_buku
                                $file_buku = explode('/', $buku_permohonan_terbit->file_buku);
                                $file_buku = end($file_buku);

                                // copy file $buku_permohonan_terbit->file_buku to public/storage/buku_final_storage
                                File::ensureDirectoryExists(base_path('public/storage/buku_final_storage'));
                                File::copy(base_path('public/storage/' . $buku_permohonan_terbit->file_buku), base_path('public/storage/buku_final_storage/' . $file_buku));

                                // get jumlah halaman in pdf
                                $pdftext = file_get_contents(base_path('public/storage/buku_final_storage/' . $file_buku));
                                $jumlah_halaman = preg_match_all("/\/Page\W/", $pdftext, $matches);

                                // make buku_dijual
                                $buku_dijual = buku_dijual::create([
                                    'kategori_id' => $data['kategori_id'],
                                    'judul' => $buku_permohonan_terbit->judul,
                                    'slug' => Str::slug($buku_permohonan_terbit->judul),
                                    'harga' => $data['harga'],
                                    'tanggal_terbit' => Carbon::now()->format('Y-m-d'),
                                    'cover_buku' => $data['cover_buku'],
                                    'deskripsi' => $buku_permohonan_terbit->deskripsi,
                                    'jumlah_halaman' => $jumlah_halaman,
                                    'bahasa' => $data['bahasa'],
                                    'penerbit' => env('APP_NAME'),
                                    'nama_file_buku' => $buku_permohonan_terbit->judul . '.pdf', // 'buku_final_storage/' . $buku_permohonan_terbit->judul . '.pdf
                                    'file_buku' => 'buku_final_storage/' . $file_buku,
                                    'isbn' => $data['isbn'],
                                    'active_flag' => 0,
                                ]);

                                // make storage_buku_dijual from $data['preview_buku']
                                foreach ($data['preview_buku'] as $key => $value) {
                                    $buku_dijual->storage_buku_dijual()->create([
                                        'tipe' => 'IMAGE',
                                        'nama_file' => $value['nama_file'],
                                        'nama_generate' => $value['nama_generate'],
                                    ]);
                                }

                                // make penulis from penulis in buku_permohonan_terbit
                                $buku_dijual->penulis()->create([
                                    'nama' => $buku_permohonan_terbit->user->nama_lengkap,
                                ]);

                                if ($buku_dijual) {
                                    // update buku_permohonan_terbit
                                    $buku_permohonan_terbit->update([
                                        'dijual' => 1,
                                    ]);

                                    Notification::make()
                                        ->success()
                                        ->title('Buku berhasil dijual, Silahkan menambah data selengkapnya di menu buku dijual sebelum upload')
                                        ->send();

                                    return;
                                }

                                Notification::make()
                                    ->danger()
                                    ->title('Proses Gagal, coba ulangi beberapa saat lagi')
                                    ->send();

                                return;
                            }
                        ),
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
