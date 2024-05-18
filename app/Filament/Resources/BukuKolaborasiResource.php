<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BukuKolaborasiResource\Pages;
use App\Models\bab_buku_kolaborasi;
use App\Models\buku_kolaborasi;
use App\Models\kategori;
use App\Models\buku_dijual;
use App\Models\buku_lunas_user;
use App\Models\user_bab_buku_kolaborasi;
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
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\File;

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

                                Forms\Components\Textarea::make('deskripsi')
                                    ->columnSpan('full')
                                    ->required(),
                            ])
                            ->columns(1),

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
                                            ->hiddenOn(['create', 'edit'])
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
                                    ->hiddenOn('edit')
                                    ->required(),
                            ])
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('File')
                            ->schema([
                                Forms\Components\FileUpload::make('cover_buku')
                                    ->helperText('Gambar direkomendasikan berukuran 192 px x 192px')
                                    ->required()
                                    ->openable()
                                    ->image()
                                    ->imageEditor()
                                    ->directory('cover_buku_kolaborasi'),
                            ]),

                        Forms\Components\Section::make('File Hak Cipta')
                            ->schema([
                                Forms\Components\FileUpload::make('file_hak_cipta')
                                    ->helperText('Diupload saat sudah menciptakan buku dijual')
                                    ->openable()
                                    ->downloadable()
                                    ->disabled()
                            ])
                            ->hiddenOn(['edit', 'create']),

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
                Tables\Filters\SelectFilter::make('kategori_id')
                    ->label('kategori')
                    ->placeholder('Semua Kategori')
                    ->options(
                        kategori::all()->pluck('nama', 'id')
                    ),
            ])
            ->actions([
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

                        if ($bab_buku_kolaborasi->count() == 0) {
                            return true;
                        }

                        // for rech to check if user_bab_buku_kolaborasi is array []
                        foreach ($bab_buku_kolaborasi as $key => $babData) {
                            if (count($babData->user_bab_buku_kolaborasi) == 0) {
                                return true;
                            }
                            foreach ($babData->user_bab_buku_kolaborasi as $key => $user_bab_buku_kolaborasi) {
                                if ($user_bab_buku_kolaborasi->user == null) {
                                    return true;
                                }
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

                        Forms\Components\FileUpload::make('cover_buku')
                            ->helperText('Gambar direkomendasikan berukuran 192 px x 192px')
                            ->required()
                            ->openable()
                            ->image()
                            ->imageEditor()
                            ->directory('cover_buku_dijual'),

                        Forms\Components\TextInput::make('isbn')
                            ->label('ISBN')
                            ->required()
                            ->maxLength(255),

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

                        Forms\Components\FileUpload::make('file_buku')
                            ->label('Upload File Buku PDF (final version)')
                            ->required()
                            ->openable()
                            ->acceptedFileTypes(['application/pdf'])
                            ->storeFileNamesIn('nama_file_buku')
                            ->directory('buku_final_storage'),

                        Forms\Components\FileUpload::make('file_hak_cipta')
                            ->label('Upload File Hak Cipta PDF (optional)')
                            ->openable()
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('hak_cipta'),
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

                            $hargaCount = 0;

                            $penulis = array();
                            $userPenulisArray = array();

                            // forech $bab_buku_kolaborasi to merge pdf file_buku in user_bab_buku_kolaborasi
                            foreach ($bab_buku_kolaborasi as $key => $babData) {
                                foreach ($babData->user_bab_buku_kolaborasi as $key => $bab_buku) {
                                    if ($bab_buku->status == 'DONE') {
                                        $hargaCount += $babData->harga;

                                        if ($bab_buku->user != null) {
                                            $penulis[] = $bab_buku->user->nama_lengkap;
                                            $userPenulisArray[] = $bab_buku->user;
                                        } else {
                                            Notification::make()
                                                ->danger()
                                                ->title('Ada User Yang Tekena Suspen Akun!')
                                                ->send();

                                            return;
                                        }
                                    } else {
                                        Notification::make()
                                            ->danger()
                                            ->title('Bab buku belum lengkap')
                                            ->send();

                                        return;
                                    }
                                }
                            }

                            // ngehitung halaman pdf ambil dari store tempatnya
                            $pdftext = file_get_contents(public_path('storage/' . $data['file_buku']));
                            $jumlah_halaman = preg_match_all("/\/Page\W/", $pdftext, $matches);

                            // make buku_dijual
                            $buku_dijual = buku_dijual::create([
                                'kategori_id' => $buku_kolaborasi->kategori_id,
                                'judul' => $buku_kolaborasi->judul,
                                'slug' => $buku_kolaborasi->slug,
                                'harga' => $data['harga'],
                                'tanggal_terbit' => Carbon::now()->format('Y-m-d'),
                                'cover_buku' => $data['cover_buku'],
                                'deskripsi' => $buku_kolaborasi->deskripsi,
                                'jumlah_halaman' => $jumlah_halaman,
                                'bahasa' => $buku_kolaborasi->bahasa,
                                'penerbit' => config('app.app_name'),
                                'nama_file_buku' => $data['nama_file_buku'],
                                'file_buku' => $data['file_buku'],
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

                                $userPenulisArray = array_unique($userPenulisArray);

                                if ($data['file_hak_cipta']) {
                                    $buku_kolaborasi->update([
                                        'file_hak_cipta' => $data['file_hak_cipta'],
                                    ]);


                                    foreach ($userPenulisArray as $key => $value) {

                                        $recipientUser = $value;

                                        Notification::make()
                                            ->success()
                                            ->title(
                                                'Buku kolaborasi ' . $buku_dijual->judul . ' sudah diterbitkan, silahkan melihat pada koleksi buku Anda dan download hak cipta anda pada koleksi kolaborasi'
                                            )
                                            ->sendToDatabase($recipientUser);

                                        event(new DatabaseNotificationsSent($recipientUser));

                                        buku_lunas_user::create([
                                            "user_id" => $value->id,
                                            "buku_dijual_id" => $buku_dijual->id
                                        ]);
                                    }
                                } else {

                                    foreach ($userPenulisArray as $key => $value) {

                                        $recipientUser = $value;

                                        Notification::make()
                                            ->success()
                                            ->title(
                                                'Buku kolaborasi ' . $buku_dijual->judul . ' sudah diterbitkan, silahkan melihat pada koleksi buku Anda'
                                            )
                                            ->sendToDatabase($recipientUser);

                                        event(new DatabaseNotificationsSent($recipientUser));

                                        buku_lunas_user::create([
                                            "user_id" => $value->id,
                                            "buku_dijual_id" => $buku_dijual->id
                                        ]);
                                    }
                                }

                                Notification::make()
                                    ->success()
                                    ->title('Buku berhasil diterbitkan, Silahkan menambah data selengkapnya di menu buku dijual sebelum upload')
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
                Tables\Actions\Action::make('Upload Hak Cipta')
                    ->hidden(function (buku_kolaborasi $buku_kolaborasi, array $data) {
                        if ($buku_kolaborasi->dijual != 1) {
                            return true;
                        }

                        if ($buku_kolaborasi->file_hak_cipta) {
                            return true;
                        }

                        return false;
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Upload Hak Cipta')
                    ->modalDescription('Upload hak cipta buku')
                    ->modalSubmitActionLabel('Upload')
                    ->color('success')
                    ->modalIcon('heroicon-o-book-open')
                    ->icon('heroicon-o-book-open')
                    ->modalIconColor('success')
                    ->form([
                        Forms\Components\FileUpload::make('file_hak_cipta')
                            ->label('Upload File Hak Cipta PDF (optional)')
                            ->openable()
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('hak_cipta'),
                    ])
                    ->action(
                        function (buku_kolaborasi $buku_kolaborasi, array $data): void {
                            if ($buku_kolaborasi->dijual != 1) {
                                Notification::make()
                                    ->danger()
                                    ->title('Buku harus diterbitkan terlebih dahulu')
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

                            $penulis = array();
                            $userPenulisArray = array();

                            // forech $bab_buku_kolaborasi to merge pdf file_buku in user_bab_buku_kolaborasi
                            foreach ($bab_buku_kolaborasi as $key => $babData) {
                                foreach ($babData->user_bab_buku_kolaborasi as $key => $bab_buku) {
                                    if ($bab_buku->status == 'DONE') {
                                        if ($bab_buku->user != null) {
                                            $penulis[] = $bab_buku->user->nama_lengkap;
                                            $userPenulisArray[] = $bab_buku->user;
                                        }
                                    }
                                }
                            }

                            if ($data['file_hak_cipta']) {
                                $buku_kolaborasi->update([
                                    'file_hak_cipta' => $data['file_hak_cipta'],
                                ]);

                                Notification::make()
                                    ->success()
                                    ->title('Hak Cipta Buku Berhasil Diupload')
                                    ->send();

                                $userPenulisArray = array_unique($userPenulisArray);

                                foreach ($userPenulisArray as $key => $value) {

                                    $recipientUser = $value;

                                    Notification::make()
                                        ->success()
                                        ->title(
                                            'Buku kolaborasi ' . $buku_kolaborasi->judul . ' Hak Cipta Berhasil Diupload, silahkan cek pada menu kolaborasi buku Anda'
                                        )
                                        ->sendToDatabase($recipientUser);

                                    event(new DatabaseNotificationsSent($recipientUser));
                                }

                                return;
                            }

                            Notification::make()
                                ->danger()
                                ->title('Proses Gagal, coba ulangi beberapa saat lagi')
                                ->send();

                            return;
                        }
                    ),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->slideOver(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->hidden(function (buku_kolaborasi $buku_kolaborasi, array $data) {
                            // get bab_buku_kolaborasi
                            $bab_buku_kolaborasi = bab_buku_kolaborasi::with([
                                'user_bab_buku_kolaborasi' => fn ($query) => $query->where('status', 'DONE')
                            ])
                                ->where('buku_kolaborasi_id', $buku_kolaborasi->id)
                                ->get();

                            // for rech to check if user_bab_buku_kolaborasi is array []
                            foreach ($bab_buku_kolaborasi as $key => $babData) {
                                if (count($babData->user_bab_buku_kolaborasi) != 0) {
                                    return true;
                                }
                                foreach ($babData->user_bab_buku_kolaborasi as $key => $user_bab_buku_kolaborasi) {
                                    if ($user_bab_buku_kolaborasi->user != null) {
                                        return true;
                                    }
                                }
                            }

                            return false;
                        }),
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
