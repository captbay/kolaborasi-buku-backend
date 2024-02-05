<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BukuKolaborasiResource\Pages;
use App\Models\bab_buku_kolaborasi;
use App\Models\buku_kolaborasi;
use App\Models\kategori;
use App\Models\buku_dijual;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;

class BukuKolaborasiResource extends Resource
{
    protected static ?string $model = buku_kolaborasi::class;

    protected static ?string $navigationLabel = 'Buku Kolaborasi';

    protected static ?string $label = 'Buku Kolaborasi';

    protected static ?string $slug = 'buku-kolaborasi';

    protected static ?string $title = 'Buku Kolaborasi';

    // protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-s-folder-open';

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
                                            ->minValue(1)
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

                                        Repeater::make('user_bab_buku_kolaborasi')
                                            ->relationship()
                                            ->schema([
                                                Forms\Components\FileUpload::make('file_bab')
                                                    // ->default(fn ($state) => user_bab_buku_kolaborasi::where('bab_buku_kolaborasi_id', $state)->first()?->file_bab)
                                                    ->helperText('* File bab yang sudah dikerjakan oleh member (diupload oleh member)')
                                                    ->disabled()
                                                    ->label(false)
                                                    ->openable()
                                                    ->downloadable()
                                                    ->columnSpan('full')
                                                    ->acceptedFileTypes(['application/pdf'])
                                                    ->directory('file_buku_bab_kolaborasi'),
                                            ])
                                            ->label('File Bab')
                                            ->addable(false)
                                            ->deleteAction(
                                                fn (Action $action) => $action->disabled(),
                                            )
                                            ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                                                // only return user_bab_buku_kolaborasi where status == DONE to repeater filament
                                                if ($data['status'] !== 'DONE') {
                                                    return [];
                                                }

                                                return $data;
                                            })
                                            ->hiddenOn('create')
                                            ->columnSpan('full'),



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
                                    ->imageEditor()
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
                    Tables\Actions\ViewAction::make()->slideOver(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('Jual Buku')
                        ->slideOver()
                        ->hidden(function (buku_kolaborasi $buku_kolaborasi, array $data) {
                            if ($buku_kolaborasi->dijual == 1) {
                                return true;
                            }

                            // get bab_buku_kolaborasi
                            $bab_buku_kolaborasi = bab_buku_kolaborasi::with([
                                'user_bab_buku_kolaborasi' => fn ($query) => $query->where('status', 'DONE')
                            ])
                                ->where('buku_kolaborasi_id', $buku_kolaborasi->id)
                                ->get();

                            // for rech to check if user_bab_buku_kolaborasi is array []
                            foreach ($bab_buku_kolaborasi as $key => $babData) {
                                if (count($babData->user_bab_buku_kolaborasi) == 0) {
                                    return true;
                                }
                            }

                            return false;
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Buku Akan Dijual')
                        ->modalDescription('Apakah sudah yakin ingin dijual? Buku ini akan dipindahkan ke Buku Dijual')
                        ->modalSubmitActionLabel('iya, jual buku')
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
                            Repeater::make('preview_buku')
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
                            function (buku_kolaborasi $buku_kolaborasi, array $data): void {
                                if ($buku_kolaborasi->dijual == 1) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Buku sudah dijual')
                                        ->send();

                                    return;
                                }

                                // get bab_buku_kolaborasi
                                $bab_buku_kolaborasi = bab_buku_kolaborasi::with([
                                    'user_bab_buku_kolaborasi' => fn ($query) => $query->where('status', 'DONE')
                                ])
                                    ->where('buku_kolaborasi_id', $buku_kolaborasi->id)
                                    ->get();

                                // for rech to check if user_bab_buku_kolaborasi is array []
                                foreach ($bab_buku_kolaborasi as $key => $babData) {
                                    if (count($babData->user_bab_buku_kolaborasi) == 0) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Bab buku belum lengkap')
                                            ->send();

                                        return;
                                    }
                                }

                                $pdf = PDFMerger::init();

                                $hargaCount = 0;

                                $penulis = array();

                                // forech $bab_buku_kolaborasi to merge pdf file_buku in user_bab_buku_kolaborasi
                                foreach ($bab_buku_kolaborasi as $key => $babData) {
                                    foreach ($babData->user_bab_buku_kolaborasi as $key => $bab_buku) {
                                        if ($bab_buku->status == 'DONE') {
                                            $filePath = base_path('public/storage/' . $bab_buku->file_bab);

                                            $pdf->addPDF($filePath, 'all');

                                            $hargaCount += $babData->harga;

                                            $penulis[] = $bab_buku->user->nama_lengkap;
                                        } else {
                                            Notification::make()
                                                ->danger()
                                                ->title('Bab buku belum lengkap')
                                                ->send();

                                            return;
                                        }
                                    }
                                }

                                $pdf->merge();

                                File::ensureDirectoryExists(base_path('public/storage/buku_final_storage'));
                                // save pdf
                                $pdf->save(base_path('public/storage/buku_final_storage/' . $buku_kolaborasi->judul . '.pdf'));


                                $pdftext = file_get_contents(base_path('public/storage/buku_final_storage/' . $buku_kolaborasi->judul . '.pdf'));
                                $jumlah_halaman = preg_match_all("/\/Page\W/", $pdftext, $matches);

                                if ($pdf) {
                                    // split name of $buku_kolaborasi->cover_buku
                                    $cover_buku = explode('/', $buku_kolaborasi->cover_buku);
                                    $cover_buku = end($cover_buku);

                                    // copy file $buku_kolaborasi->cover_buku to public/storage/cover_buku_dijual
                                    File::ensureDirectoryExists(base_path('public/storage/cover_buku_dijual'));
                                    File::copy(base_path('public/storage/' . $buku_kolaborasi->cover_buku), base_path('public/storage/cover_buku_dijual/' . $cover_buku));

                                    // make buku_dijual
                                    $buku_dijual = buku_dijual::create([
                                        'kategori_id' => $buku_kolaborasi->kategori_id,
                                        'judul' => $buku_kolaborasi->judul,
                                        'slug' => $buku_kolaborasi->slug,
                                        'harga' => $data['harga'],
                                        'tanggal_terbit' => Carbon::now()->format('Y-m-d'),
                                        'cover_buku' => 'cover_buku_dijual/' . $cover_buku,
                                        'deskripsi' => $buku_kolaborasi->deskripsi,
                                        'jumlah_halaman' => $jumlah_halaman,
                                        'bahasa' => $buku_kolaborasi->bahasa,
                                        'penerbit' => env('APP_NAME'),
                                        'nama_file_buku' => $buku_kolaborasi->judul . '.pdf', // 'buku_final_storage/' . $buku_kolaborasi->judul . '.pdf
                                        'file_buku' => 'buku_final_storage/' . $buku_kolaborasi->judul . '.pdf',
                                        'jumlah_bab' => $buku_kolaborasi->jumlah_bab,
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

                                    // make penulis from penulis array
                                    $penulis = array_unique($penulis);
                                    foreach ($penulis as $key => $value) {
                                        $buku_dijual->penulis()->create([
                                            'nama' => $value,
                                        ]);
                                    }

                                    if ($buku_dijual) {
                                        // update buku_kolaborasi
                                        $buku_kolaborasi->update([
                                            'dijual' => 1,
                                        ]);

                                        Notification::make()
                                            ->success()
                                            ->title('Buku berhasil dijual, Silahkan menambah data selengkapnya di menu buku dijual sebeum upload')
                                            ->send();

                                        return;
                                    }
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
            'index' => Pages\ListBukuKolaborasis::route('/'),
            'create' => Pages\CreateBukuKolaborasi::route('/create'),
            'edit' => Pages\EditBukuKolaborasi::route('/{record}/edit'),
        ];
    }
}
