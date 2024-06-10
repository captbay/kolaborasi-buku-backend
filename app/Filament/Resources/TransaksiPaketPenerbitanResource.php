<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiPaketPenerbitanResource\Pages;
use App\Jobs\CheckIsDeadline;
use App\Models\buku_dijual;
use App\Models\buku_lunas_user;
use App\Models\jasa_tambahan;
use App\Models\paket_penerbitan;
use App\Models\transaksi_paket_penerbitan;
use Carbon\Carbon;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Illuminate\Support\Str;
use Filament\Tables\Table;
use App\Models\buku_permohonan_terbit;
use App\Models\kategori;
use App\Models\penulis;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Number;

class TransaksiPaketPenerbitanResource extends Resource
{
    protected static ?string $model = transaksi_paket_penerbitan::class;

    protected static ?string $navigationLabel = 'Transaksi Paket Penerbitan';

    protected static ?string $label = 'Transaksi Paket Penerbitan';

    protected static ?string $slug = 'transaksi-paket-penerbitan';

    protected static ?string $title = 'Transaksi Paket Penerbitan';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-s-currency-dollar';

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
                                Forms\Components\DateTimePicker::make('created_at')
                                    ->label('Tanggal Dibuat Transaksi')
                                    ->disabled(),
                                Forms\Components\TextInput::make('no_transaksi')
                                    ->disabled(),
                                Forms\Components\DateTimePicker::make('date_time_dp_lunas')
                                    ->label('Tanggal DP Lunas')
                                    ->disabled(),

                                Forms\Components\DateTimePicker::make('date_time_lunas')
                                    ->label('Tanggal Pelunasan')
                                    ->disabled(),

                                Forms\Components\FileUpload::make('dp_upload')
                                    ->label('DP Bukti Bayar')
                                    ->openable()
                                    ->downloadable()
                                    ->disabled(),

                                Forms\Components\FileUpload::make('pelunasan_upload')
                                    ->label('Pelunasan Bukti Bayar')
                                    ->openable()
                                    ->downloadable()
                                    ->disabled(),

                                Forms\Components\TextInput::make('status')
                                    ->label('Status Transaksi')
                                    ->columnSpan('full')
                                    ->disabled(),

                                Forms\Components\Fieldset::make('Data Pembeli')
                                    ->relationship('user')
                                    ->schema([
                                        Forms\Components\TextInput::make('nama_lengkap')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('email')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('no_telepon')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('alamat')
                                            ->disabled(),
                                    ])
                            ])
                            ->columns(2),

                        Forms\Components\Fieldset::make('Data Buku Permohonan Terbit')
                            ->relationship('buku_permohonan_terbit')
                            ->schema([
                                Forms\Components\TextInput::make('judul')
                                    ->label('Judul Buku')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(buku_permohonan_terbit::class, 'judul', ignoreRecord: true),

                                Forms\Components\TextInput::make('isbn')
                                    ->label('ISBN')
                                    ->disabled(),

                                Forms\Components\Textarea::make('deskripsi')
                                    ->columnSpan('full')
                                    ->required(),

                                Forms\Components\FileUpload::make('file_mou')
                                    ->label('MOU')
                                    ->disabled()
                                    ->openable()
                                    ->columnSpan('full')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->downloadable()
                                    ->directory('mou_paket_penerbitan'),

                                Forms\Components\FileUpload::make('cover_buku')
                                    ->label('Draft Cover Buku')
                                    ->openable()
                                    ->image()
                                    ->imageEditor()
                                    ->directory('cover_buku_permohonan_terbit')
                                    ->columnSpan('full')
                                    ->downloadable(),

                                Forms\Components\FileUpload::make('file_buku')
                                    ->label('Draft Buku')
                                    ->required()
                                    ->openable()
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->directory('buku_permohonan_terbit')
                                    ->columnSpan('full')
                                    ->downloadable(),
                            ]),

                        Forms\Components\Section::make('Data Paket Penerbitan')
                            ->schema([
                                Forms\Components\Select::make('nama')
                                    ->relationship('paket_penerbitan')
                                    ->searchable()
                                    ->preload()
                                    ->live(onBlur: true)
                                    ->getOptionLabelFromRecordUsing(fn (paket_penerbitan $record) => "Nama Paket: " . $record->nama . " - Harga: " . Number::currency($record->harga, 'IDR'))
                                    ->required(),
                            ]),

                        Forms\Components\Section::make('Data Jasa Tambahan')
                            ->schema([
                                Forms\Components\Repeater::make('trx_jasa_penerbitan')
                                    ->relationship()
                                    ->label(false)
                                    ->schema([
                                        Forms\Components\Select::make('jasa_tambahan_id')
                                            ->relationship('jasa_tambahan', 'nama')
                                            ->searchable()
                                            ->preload()
                                            ->live(onBlur: true)
                                            ->getOptionLabelFromRecordUsing(fn (jasa_tambahan $record) => "Nama Jasa: " . $record->nama . " - Harga: " . Number::currency($record->harga, 'IDR'))
                                            ->required()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->label('Jasa'),
                                    ]),
                            ]),

                        Forms\Components\Section::make('Keterangan Untuk Member (optional)')
                            ->schema([
                                Forms\Components\Textarea::make('note')
                                    ->label(false)
                                    ->disabled()
                                    ->live(onBlur: true)
                                    ->helperText('* Note atau pemberitahuan untuk member')
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_transaksi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.nama_lengkap')
                    ->label('Nama Lengkap')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('paket_penerbitan.nama')
                    ->label('Paket Penerbitan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buku_permohonan_terbit.judul')
                    ->label('Judul Buku')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga')
                    ->searchable()
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(
                        fn (transaksi_paket_penerbitan $transaksi_paket_penerbitan) => match ($transaksi_paket_penerbitan->status) {
                            'REVIEW' => 'gray',
                            'TERIMA DRAFT' => 'info',
                            'DP UPLOADED' => 'primary',
                            'DP TIDAK SAH' => 'danger',
                            'INPUT ISBN' => 'warning',
                            'DRAFT SELESAI' => Color::Indigo,
                            'PELUNASAN UPLOADED' => 'primary',
                            'PELUNASAN TIDAK SAH' => 'danger',
                            'SIAP TERBIT' => 'success',
                            'SUDAH TERBIT' => 'success',
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
                    Tables\Actions\Action::make('Berikan Note')
                        ->icon('heroicon-s-document-check')
                        ->hidden(function (transaksi_paket_penerbitan $record) {
                            if ($record->status == 'DP TIDAK SAH' || $record->status == 'PELUNASAN TIDAK SAH') {
                                return true;
                            }

                            return false;
                        })
                        ->form([
                            Forms\Components\Textarea::make('note')
                                ->required()
                                ->label(false)
                                ->live(onBlur: true)
                                ->helperText('* Note atau pemberitahuan untuk member')
                        ])
                        ->action(function (transaksi_paket_penerbitan $record, array $data) {
                            $record->update([
                                'note' => $data['note'],
                            ]);

                            Notification::make()
                                ->success()
                                ->title(
                                    'Note berhasil diberikan kepada ' . $record->user->nama_lengkap . '!'
                                )
                                ->send();

                            $recipientUser = $record->user;

                            // send notif to user yang bayar
                            Notification::make()
                                ->success()
                                ->title(
                                    'Note Penerbitan Baru dari Admin'
                                )
                                ->body(
                                    'Ada Pemberitahuan dari admin penerbitan untuk penerbitan buku ' . $record->buku_permohonan_terbit->judul . '!'
                                )
                                ->sendToDatabase($recipientUser);

                            event(new DatabaseNotificationsSent($recipientUser));
                        }),
                    // siap terbit
                    Tables\Actions\Action::make('Terbitkan')
                        ->slideOver()
                        ->hidden(function (transaksi_paket_penerbitan $transaksi) {
                            if ($transaksi->status != 'SIAP TERBIT') {
                                return true;
                            }

                            if ($transaksi->user == null) {
                                return true;
                            }

                            // if dijual == 1
                            if ($transaksi->buku_permohonan_terbit->dijual == 1) {
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

                            Forms\Components\Select::make('penulis_id')
                                ->options(penulis::all()->pluck('nama', 'id'))
                                ->multiple()
                                ->preload()
                                ->label('Penulis')
                                ->default(
                                    function (transaksi_paket_penerbitan $transaksi) {
                                        return [$transaksi->user->nama_lengkap];
                                    }
                                )
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('nama')
                                        ->unique(penulis::class, 'nama', ignoreRecord: true)
                                        ->required(),
                                ])
                                ->createOptionUsing(function (array $data) {
                                    if ($data['nama']) {
                                        return penulis::create([
                                            'nama' => $data['nama']
                                        ])->getKey();
                                    }
                                    return null;
                                })
                                ->createOptionAction(function (Action $action) {
                                    return $action
                                        ->modalHeading('Buat Data Penulis')
                                        ->modalSubmitActionLabel('Buat penulis')
                                        ->modalWidth('lg');
                                }),

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
                            function (transaksi_paket_penerbitan $transaksi, array $data): void {
                                $buku_permohonan_terbit = $transaksi->buku_permohonan_terbit;

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
                                    'penerbit' => config('app.app_name'),
                                    'nama_file_buku' => $buku_permohonan_terbit->judul . '.pdf', // 'buku_final_storage/' . $buku_permohonan_terbit->judul . '.pdf
                                    'file_buku' => 'buku_final_storage/' . $file_buku,
                                    'isbn' => $buku_permohonan_terbit->isbn,
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

                                // make penulis from penulis_id
                                foreach ($data['penulis_id'] as $key => $value) {
                                    // check the value if exists in penulis table
                                    $penulis = penulis::find($value);
                                    if ($penulis) {
                                        $buku_dijual->penulis()->attach($value);
                                    } else {
                                        $penulis = penulis::create([
                                            'nama' => $value,
                                        ]);
                                        $buku_dijual->penulis()->attach($penulis->id);
                                    }
                                }

                                if ($buku_dijual) {
                                    // update buku_permohonan_terbit
                                    $buku_permohonan_terbit->update([
                                        'dijual' => 1,
                                    ]);

                                    // update status transaksi_paket_penerbitan
                                    $transaksi->update([
                                        'status' => 'SUDAH TERBIT',
                                    ]);

                                    // add buku to user_buku_lunas
                                    buku_lunas_user::create([
                                        'user_id' => $transaksi->user_id,
                                        'buku_dijual_id' => $buku_dijual->id,
                                    ]);

                                    $recipientAdmin = auth()->user();

                                    Notification::make()
                                        ->success()
                                        ->title('Penerbitan untuk permohonan terbit berhasil, No Transaksi ' . $transaksi->no_transaksi . ' Sudah TERBIT')
                                        ->sendToDatabase($recipientAdmin)
                                        ->send();

                                    $recipientUser = $transaksi->user;

                                    // send notif to user yang bayar
                                    Notification::make()
                                        ->success()
                                        ->title(
                                            'Penerbitan permohonan terbit dengan No Transaksi '
                                                . $transaksi->no_transaksi .
                                                ' berhasil, Buku anda sudah terbit. Terima kasih.'
                                        )
                                        ->body($transaksi->id)
                                        ->sendToDatabase($recipientUser);

                                    event(new DatabaseNotificationsSent($recipientUser));

                                    return;
                                }

                                Notification::make()
                                    ->danger()
                                    ->title('Proses Gagal, coba ulangi beberapa saat lagi')
                                    ->send();

                                return;
                            }
                        ),

                    // input ISBN
                    Tables\Actions\Action::make('Input ISBN')
                        ->label('Input ISBN')
                        ->slideOver()
                        ->hidden(function (transaksi_paket_penerbitan $transaksi) {
                            if ($transaksi->status != 'INPUT ISBN') {
                                return true;
                            }

                            return false;
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Input ISBN')
                        ->modalDescription('Apakah anda yakin ingin memasukkan ISBN buku ini?')
                        ->modalSubmitActionLabel('iya, input ISBN')
                        ->color('success')
                        ->modalIcon('heroicon-o-book-open')
                        ->icon('heroicon-o-book-open')
                        ->modalIconColor('success')
                        ->form([
                            Forms\Components\TextInput::make('isbn')
                                ->label('ISBN')
                                ->required(),
                        ])
                        ->action(
                            function (transaksi_paket_penerbitan $transaksi, array $data): void {
                                $buku_permohonan_terbit = $transaksi->buku_permohonan_terbit;

                                $buku_permohonan_terbit->update([
                                    'isbn' => $data['isbn'],
                                ]);

                                if ($buku_permohonan_terbit->isbn != null) {
                                    // update status transaksi_paket_penerbitan
                                    $transaksi->update([
                                        'status' => 'DRAFT SELESAI',
                                        'date_time_exp' => Carbon::now()->addHours(24),
                                    ]);

                                    $recipientAdmin = auth()->user();

                                    Notification::make()
                                        ->success()
                                        ->title('Input ISBN untuk permohonan terbit berhasil, No Transaksi ' . $transaksi->no_transaksi . ' Sudah DRAFT SELESAI')
                                        ->sendToDatabase($recipientAdmin)
                                        ->send();

                                    $recipientUser = $transaksi->user;

                                    CheckIsDeadline::dispatch($transaksi->id, 'paket')->delay($transaksi->date_time_exp);

                                    // send notif to user yang bayar
                                    Notification::make()
                                        ->success()
                                        ->title(
                                            'Input ISBN permohonan terbit dengan No Transaksi '
                                                . $transaksi->no_transaksi .
                                                ' berhasil, lakukan pelunasan untuk segera diterbitkan'
                                        )
                                        ->body($transaksi->id)
                                        ->sendToDatabase($recipientUser);

                                    event(new DatabaseNotificationsSent($recipientUser));

                                    return;
                                }

                                Notification::make()
                                    ->danger()
                                    ->title('Proses Gagal, coba ulangi beberapa saat lagi')
                                    ->send();

                                return;
                            }
                        ),

                    // verifikasi
                    Tables\Actions\Action::make('Verifikasi')
                        ->hidden(function (transaksi_paket_penerbitan $transaksi) {
                            if ($transaksi->dp_upload == null && $transaksi->pelunasan_upload == null) {
                                return true;
                            }
                            if ($transaksi->status != 'DP UPLOADED' && $transaksi->status != 'PELUNASAN UPLOADED') {
                                return true;
                            }
                            return false;
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Verifikasi Pembayaran')
                        ->modalDescription('Apakah anda yakin ingin memverifikasi pembayaran ini, mohon cek dahulu bukti bayar?')
                        ->modalSubmitActionLabel('iya, verifikasi')
                        ->color('success')
                        ->modalIcon('heroicon-s-chat-bubble-left-ellipsis')
                        ->icon('heroicon-s-document-check')
                        ->modalIconColor('success')
                        ->form([
                            Forms\Components\FileUpload::make('foto_bukti_bayar')
                                ->label('Foto Bukti Bayar')
                                ->default(
                                    function (transaksi_paket_penerbitan $transaksi) {
                                        if ($transaksi->status == 'DP UPLOADED') {
                                            return $transaksi->dp_upload;
                                        } else if ($transaksi->status == 'PELUNASAN UPLOADED') {
                                            return $transaksi->pelunasan_upload;
                                        } else {
                                            return null;
                                        }
                                    }
                                )
                                ->openable()
                                ->downloadable()
                                ->image()
                                ->imagePreviewHeight('500')
                                ->panelAspectRatio('1:1')
                                ->disabled(),
                        ])
                        ->action(
                            function (transaksi_paket_penerbitan $transaksi): void {
                                if ($transaksi->status == 'DP UPLOADED') {
                                    if ($transaksi->date_time_dp_lunas != null) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Customer ini sudah lunas')
                                            ->send();

                                        return;
                                    }

                                    if ($transaksi->dp_upload == null) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Foto bukti bayar tidak ada')
                                            ->send();
                                    }

                                    // update status and datetimelunas
                                    $transaksi->update([
                                        'status' => 'INPUT ISBN',
                                        'date_time_dp_lunas' => Carbon::now(),
                                    ]);

                                    $recipientAdmin = auth()->user();

                                    Notification::make()
                                        ->success()
                                        ->title('DP untuk permohonan terbit berhasil diverifikasi, No Transaksi ' . $transaksi->no_transaksi . ' Sudah LUNAS DP')
                                        ->sendToDatabase($recipientAdmin)
                                        ->send();

                                    $recipientUser = $transaksi->user;

                                    // send notif to user yang bayar
                                    Notification::make()
                                        ->success()
                                        ->title(
                                            'DP permohonan terbit dengan No Transaksi '
                                                . $transaksi->no_transaksi .
                                                ' berhasil, kami segera akan menyelesaikan INPUT ISBN'
                                        )
                                        ->body($transaksi->id)
                                        ->sendToDatabase($recipientUser);

                                    event(new DatabaseNotificationsSent($recipientUser));

                                    return;
                                } else if ($transaksi->status == 'PELUNASAN UPLOADED') {
                                    if ($transaksi->date_time_lunas != null) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Customer ini sudah lunas')
                                            ->send();

                                        return;
                                    }

                                    if ($transaksi->pelunasan_upload == null) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Foto bukti bayar tidak ada')
                                            ->send();
                                    }

                                    // update status and datetimelunas
                                    $transaksi->update([
                                        'status' => 'SIAP TERBIT',
                                        'date_time_lunas' => Carbon::now(),
                                    ]);

                                    $recipientAdmin = auth()->user();

                                    Notification::make()
                                        ->success()
                                        ->title('Pelunasan untuk permohonan terbit berhasil diverifikasi, No Transaksi ' . $transaksi->no_transaksi . ' Sudah LUNAS')
                                        ->sendToDatabase($recipientAdmin)
                                        ->send();

                                    $recipientUser = $transaksi->user;

                                    // send notif to user yang bayar
                                    Notification::make()
                                        ->success()
                                        ->title(
                                            'Pelunasan permohonan terbit dengan No Transaksi '
                                                . $transaksi->no_transaksi .
                                                ' berhasil, Buku anda segera akan terbit. Terima kasih.'
                                        )
                                        ->body($transaksi->id)
                                        ->sendToDatabase($recipientUser);

                                    event(new DatabaseNotificationsSent($recipientUser));

                                    return;
                                } else {
                                    return;
                                }
                            }
                        ),
                    // batalkan verifikasi
                    Tables\Actions\Action::make('Tidak Sesuai')
                        ->hidden(function (transaksi_paket_penerbitan $transaksi) {
                            if ($transaksi->dp_upload == null && $transaksi->pelunasan_upload == null) {
                                return true;
                            }
                            if ($transaksi->status != 'DP UPLOADED' && $transaksi->status != 'PELUNASAN UPLOADED') {
                                return true;
                            }
                            return false;
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Batalkan Verifikasi Pembayaran')
                        ->modalDescription('Apakah anda yakin ingin membatalkan verifikasi pembayaran ini?')
                        ->modalSubmitActionLabel('iya, batalkan')
                        ->color('danger')
                        ->modalIcon('heroicon-s-chat-bubble-left-ellipsis')
                        ->icon('heroicon-s-document-check')
                        ->modalIconColor('danger')
                        ->form([
                            Forms\Components\FileUpload::make('foto_bukti_bayar')
                                ->label('Foto Bukti Bayar')
                                ->default(
                                    function (transaksi_paket_penerbitan $transaksi) {
                                        if ($transaksi->status == 'DP UPLOADED') {
                                            return $transaksi->dp_upload;
                                        } else if ($transaksi->status == 'PELUNASAN UPLOADED') {
                                            return $transaksi->pelunasan_upload;
                                        } else {
                                            return null;
                                        }
                                    }
                                )
                                ->openable()
                                ->downloadable()
                                ->image()
                                ->imagePreviewHeight('500')
                                ->panelAspectRatio('1:1')
                                ->disabled(),
                        ])
                        ->action(
                            function (transaksi_paket_penerbitan $transaksi): void {
                                if ($transaksi->status == 'DP UPLOADED') {
                                    if ($transaksi->date_time_dp_lunas != null) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Customer ini sudah lunas')
                                            ->send();

                                        return;
                                    }

                                    if ($transaksi->dp_upload == null) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Foto bukti bayar tidak ada')
                                            ->send();
                                    }

                                    // update status and datetimelunas
                                    $transaksi->update([
                                        'status' => 'DP TIDAK SAH',
                                        'date_time_exp' => null,
                                        'date_time_dp_lunas' => null,
                                    ]);

                                    $recipientAdmin = auth()->user();

                                    Notification::make()
                                        ->success()
                                        ->title('DP untuk permohonan terbit tidak sah, No Transaksi ' . $transaksi->no_transaksi . ' Sudah DIBATALKAN')
                                        ->sendToDatabase($recipientAdmin)
                                        ->send();

                                    $recipientUser = $transaksi->user;

                                    // send notif to user yang bayar
                                    Notification::make()
                                        ->success()
                                        ->title(
                                            'DP permohonan terbit dengan No Transaksi '
                                                . $transaksi->no_transaksi .
                                                ' gagal, mohon segera melakukan pembayaran ulang.'
                                        )
                                        ->body($transaksi->id)
                                        ->sendToDatabase($recipientUser);

                                    event(new DatabaseNotificationsSent($recipientUser));

                                    return;
                                } else if ($transaksi->status == 'PELUNASAN UPLOADED') {
                                    if ($transaksi->date_time_lunas != null) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Customer ini sudah lunas')
                                            ->send();

                                        return;
                                    }

                                    if ($transaksi->pelunasan_upload == null) {
                                        Notification::make()
                                            ->danger()
                                            ->title('Foto bukti bayar tidak ada')
                                            ->send();
                                    }

                                    // update status and datetimelunas
                                    $transaksi->update([
                                        'status' => 'PELUNASAN TIDAK SAH',
                                        'date_time_exp' => null,
                                        'date_time_lunas' => null,
                                    ]);

                                    $recipientAdmin = auth()->user();

                                    Notification::make()
                                        ->success()
                                        ->title('Pelunasan untuk permohonan terbit tidak sah, No Transaksi ' . $transaksi->no_transaksi . ' Sudah DIBATALKAN')
                                        ->sendToDatabase($recipientAdmin)
                                        ->send();

                                    $recipientUser = $transaksi->user;

                                    // send notif to user yang bayar
                                    Notification::make()
                                        ->success()
                                        ->title(
                                            'Pelunasan permohonan terbit dengan No Transaksi '
                                                . $transaksi->no_transaksi .
                                                ' gagal, mohon segera melakukan pembayaran ulang.'
                                        )
                                        ->body($transaksi->id)
                                        ->sendToDatabase($recipientUser);

                                    event(new DatabaseNotificationsSent($recipientUser));

                                    return;
                                } else {
                                    return;
                                }
                            }
                        ),
                    // sunting
                    Tables\Actions\EditAction::make()
                        ->label('Sunting')
                        ->hidden(function (transaksi_paket_penerbitan $transaksi) {
                            if ($transaksi->status != 'REVIEW') {
                                return true;
                            }
                            return false;
                        }),
                    // lanjut tahap Terima Draft
                    Tables\Actions\Action::make('Terima Draft')
                        ->hidden(function (transaksi_paket_penerbitan $transaksi) {
                            if ($transaksi->status != 'REVIEW') {
                                return true;
                            }
                            return false;
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Terima Draft')
                        ->modalDescription('Apakah anda yakin ingin menerima draft ini?')
                        ->modalSubmitActionLabel('iya, Terima Draft')
                        ->color('success')
                        ->modalIcon('heroicon-s-chat-bubble-left-ellipsis')
                        ->icon('heroicon-s-document-check')
                        ->modalIconColor('success')
                        ->action(
                            function (transaksi_paket_penerbitan $transaksi): void {
                                $transaksi->update([
                                    'status' => 'TERIMA DRAFT',
                                    'date_time_exp' => Carbon::now()->addHours(24),
                                ]);

                                $recipientAdmin = auth()->user();

                                Notification::make()
                                    ->success()
                                    ->title('Buku Permohonan Terbit: No Transaksi ' . $transaksi->no_transaksi . ' Sudah TERIMA DRAFT')
                                    ->sendToDatabase($recipientAdmin)
                                    ->send();

                                $recipientUser = $transaksi->user;

                                CheckIsDeadline::dispatch($transaksi->id, 'paket')->delay($transaksi->date_time_exp);

                                // send notif to user yang bayar
                                Notification::make()
                                    ->success()
                                    ->title('Buku Permohonan Terbit : No Transaksi ' . $transaksi->no_transaksi . ' Sudah Diterima, mohon segera melakukan pembayaran DP dalam 24 jam untuk Buku Permohonan Terbit ' .
                                        $transaksi->buku_permohonan_terbit->judul . '.')
                                    ->body($transaksi->id)
                                    ->sendToDatabase($recipientUser);

                                event(new DatabaseNotificationsSent($recipientUser));

                                return;
                            }
                        ),

                ])->iconButton()
            ])
            ->recordUrl(false)
            ->bulkActions([]);
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
            'index' => Pages\ListTransaksiPaketPenerbitans::route('/'),
            // 'create' => Pages\CreateTransaksiPaketPenerbitan::route('/create'),
            'edit' => Pages\EditTransaksiPaketPenerbitan::route('/{record}/edit'),
        ];
    }
}
