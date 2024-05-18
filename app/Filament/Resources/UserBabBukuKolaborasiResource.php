<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserBabBukuKolaborasiResource\Pages;
use App\Filament\Resources\UserBabBukuKolaborasiResource\RelationManagers;
use App\Models\bab_buku_kolaborasi;
use App\Models\user_bab_buku_kolaborasi;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class UserBabBukuKolaborasiResource extends Resource
{
    protected static ?string $model = user_bab_buku_kolaborasi::class;

    protected static ?string $navigationLabel = 'User Buku Kolaborasi';

    protected static ?string $label = 'User Buku Kolaborasi';

    protected static ?string $slug = 'user-buku-kolaborasi';

    protected static ?string $title = 'User Buku Kolaborasi';

    // protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-s-user';

    // navigation groups

    protected static ?string $navigationGroup = 'Buku Kolaborasi';

    public static function form(Form $form): Form
    {
        return $form
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

                Forms\Components\Section::make('Bab Yang Dikerjakan')
                    ->schema([
                        Forms\Components\Select::make('bab_buku_kolaborasi_id')
                            ->relationship(
                                name: 'bab_buku_kolaborasi',
                                modifyQueryUsing: fn (Builder $query) =>
                                // bab yang diambil hanya bab yang belum dibayar lunas ataupun belum diassign ke user
                                $query->whereDoesntHave('transaksi_kolaborasi_buku')
                                    ->orWhereHas('user_bab_buku_kolaborasi', function ($query) {
                                        $query->where('status', 'FAILED');
                                    })
                            )
                            ->searchable()
                            ->getSearchResultsUsing(
                                fn (string $search): array =>
                                bab_buku_kolaborasi::where('judul', 'like', "%{$search}%")
                                    ->where('no_bab', 'like', "%{$search}%")
                                    ->whereHas('buku_kolaborasi', fn ($query) => $query->where('judul', 'like', "%{$search}%"))
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(
                                        fn ($bab_buku_kolaborasi) =>
                                        [$bab_buku_kolaborasi->id => $bab_buku_kolaborasi->buku_kolaborasi->name . ' - ' . $bab_buku_kolaborasi->no_bab . ' - ' . $bab_buku_kolaborasi->judul]
                                    )
                                    ->toArray()
                            )
                            ->getOptionLabelFromRecordUsing(fn (bab_buku_kolaborasi $record) => "Judul Buku: {$record->buku_kolaborasi->judul} - Bab ke: {$record->no_bab} - Judul Bab: {$record->judul}")
                            ->getOptionLabelUsing(
                                (function ($value): ?string {
                                    $bab_buku_kolaborasi = bab_buku_kolaborasi::with('buku_kolaborasi')->find($value);

                                    return 'Judul Buku: ' . $bab_buku_kolaborasi->buku_kolaborasi->judul . ' - Bab ke: ' . $bab_buku_kolaborasi->no_bab . ' - Judul Bab: ' . $bab_buku_kolaborasi->judul;
                                })
                            )
                            ->preload()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                $bab = bab_buku_kolaborasi::find($state);

                                $datetime =  Carbon::now()->addDays($bab->durasi_pembuatan)->format('Y-m-d H:i:s');

                                $set('datetime_deadline', $datetime);
                            })
                            ->label(false)
                            ->helperText('* Bab yang diambil hanya bab yang belum ada di transaksi dan user gagal membuat bab sebelum deadlinenya')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Tanggal Tenggat Pembuatan')
                    ->schema([
                        Forms\Components\DateTimePicker::make('datetime_deadline')
                            ->label(false)
                            ->disabled()
                            ->dehydrated()
                            ->reactive()
                            ->live(onBlur: true)
                            ->helperText('* Durasi pembuatan bab yang dipilih + tanggal sekarang')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(false)
                            ->searchable()
                            ->default('PROGRESS')
                            ->options([
                                'PROGRESS' => 'Progress',
                                // 'UPLOADED' => 'Uploaded',
                                // 'EDITING' => 'Editing',
                                // 'DONE' => 'Done',
                                // 'REJECTED' => 'Rejected',
                                // 'FAILED' => 'Failed',
                            ])
                            ->disabled()
                            ->live()
                            ->required(),
                    ]),

                Forms\Components\Section::make('Keterangan Untuk Member')
                    ->schema([
                        Forms\Components\Textarea::make('note')
                            ->label(false)
                            ->live(onBlur: true)
                            ->helperText('* Note atau pemberitahuan untuk member')
                    ]),

                Forms\Components\Section::make('File')
                    ->schema([
                        Forms\Components\FileUpload::make('file_bab')
                            ->label(false)
                            ->helperText('* File bab yang sudah dikerjakan oleh member (diupload oleh member)')
                            ->disabled()
                            ->openable()
                            ->downloadable()
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('file_buku_bab_kolaborasi'),
                    ])->hiddenOn('create'),

                Forms\Components\Section::make('File Mou')
                    ->schema([
                        Forms\Components\FileUpload::make('file_mou')
                            ->label('Upload file MOU')
                            ->helperText('* File mou yang diupload oleh member')
                            ->disabled()
                            ->openable()
                            ->maxSize(2 * 1024)
                            ->downloadable()
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('mou_buku_kolaborasi'),
                    ])->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.nama_lengkap')
                    ->label('Nama Lengkap')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('bab_buku_kolaborasi.buku_kolaborasi.judul')
                    ->label('Judul Buku')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bab_buku_kolaborasi.judul')
                    ->label('Judul Bab')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('datetime_deadline')
                    ->label('Tanggal Waktu Deadline')
                    ->formatStateUsing(function (user_bab_buku_kolaborasi $user) {
                        return Carbon::parse($user->datetime_deadline)->format('d F Y // H:i');
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(
                        fn (user_bab_buku_kolaborasi $user_bab_buku_kolaborasi) => match ($user_bab_buku_kolaborasi->status) {
                            'PROGRESS' => 'primary',
                            'UPLOADED' => 'info',
                            'EDITING' => 'warning',
                            'DONE' => 'success',
                            'REJECTED' => 'danger',
                            'FAILED' => 'danger',
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
                    Tables\Actions\Action::make('Terima Kolaborasi')
                        ->requiresConfirmation()
                        ->modalHeading('Terima Kolaborasi')
                        ->modalDescription('Apakah anda yakin ingin terima bab buku kolaborasi ini?
                         jika memiliki final bab untuk kolaborasi ini silahkan input,
                          jika tidak silahkan langsung klik tombol terima')
                        ->modalSubmitActionLabel('iya, terima')
                        ->color('success')
                        ->modalIcon('heroicon-s-chat-bubble-left-ellipsis')
                        ->icon('heroicon-s-document-check')
                        ->modalIconColor('success')
                        ->hidden(function (user_bab_buku_kolaborasi $record) {
                            if ($record->status != 'EDITING') {
                                return true;
                            }

                            return false;
                        })
                        ->form([
                            Forms\Components\FileUpload::make('file_bab')
                                ->label(false)
                                ->helperText('* Optional')
                                ->openable()
                                ->downloadable()
                                ->acceptedFileTypes(['application/pdf'])
                                ->directory('file_buku_bab_kolaborasi'),
                        ])
                        ->action(function (user_bab_buku_kolaborasi $record, array $data) {
                            if ($data['file_bab'] != null && $record->file_bab != null) {
                                // delete old file
                                $filesystem = Storage::disk('public');
                                $filesystem->delete($record->file_bab);

                                $record->update([
                                    'status' => 'DONE',
                                    'file_bab' => $data['file_bab'],
                                    'datetime_deadline' => null,
                                ]);
                            } else {
                                $record->update([
                                    'status' => 'DONE',
                                    'datetime_deadline' => null,
                                ]);
                            }

                            Notification::make()
                                ->success()
                                ->title(
                                    'Kolaborasi Bab ' . $record->user->nama_lengkap . ' berhasil diterima, status sudah done!'
                                )
                                ->send();

                            $recipientUser = $record->user;

                            // send notif to user yang bayar
                            Notification::make()
                                ->success()
                                ->title(
                                    'Yeay Kolaborasi Anda sudah diterima!'
                                )
                                ->body(
                                    'Kolaborasi Buku ' . $record->bab_buku_kolaborasi->buku_kolaborasi->judul . ' telah diterima, silahkan menunggu buku untuk terbit!'
                                )
                                ->sendToDatabase($recipientUser);

                            event(new DatabaseNotificationsSent($recipientUser));
                        }),
                    Tables\Actions\Action::make('Editing Kolaborasi')
                        ->requiresConfirmation()
                        ->modalHeading('Editing Kolaborasi')
                        ->modalDescription('Apakah anda yakin ingin revisi bab buku kolaborasi ini?')
                        ->modalSubmitActionLabel('iya, revisi')
                        ->color('success')
                        ->modalIcon('heroicon-s-chat-bubble-left-ellipsis')
                        ->icon('heroicon-s-document-check')
                        ->modalIconColor('success')
                        ->hidden(function (user_bab_buku_kolaborasi $record) {
                            if ($record->status != 'UPLOADED' && $record->status != 'REJECTED') {
                                return true;
                            }

                            return false;
                        })
                        ->action(function (user_bab_buku_kolaborasi $record) {
                            $record->update([
                                'status' => 'EDITING',
                                'datetime_deadline' => null,
                            ]);

                            Notification::make()
                                ->success()
                                ->title(
                                    'Kolaborasi Bab ' . $record->user->nama_lengkap . ' berhasil masuk tahap revisi!'
                                )
                                ->send();

                            $recipientUser = $record->user;

                            // send notif to user yang bayar
                            Notification::make()
                                ->success()
                                ->title(
                                    'Yeay Kolaborasi Anda sudah masuk tahap revisi!'
                                )
                                ->body(
                                    'Kolaborasi Buku ' . $record->bab_buku_kolaborasi->buku_kolaborasi->judul . ' telah masuk tahap revisi, silahkan menunggu untuk proses selanjutnya!'
                                )
                                ->sendToDatabase($recipientUser);

                            event(new DatabaseNotificationsSent($recipientUser));
                        }),
                    Tables\Actions\Action::make('Reject Kolaborasi')
                        ->requiresConfirmation()
                        ->modalHeading('Reject Kolaborasi')
                        ->modalDescription('Apakah anda yakin ingin reject/menolak bab buku kolaborasi ini?')
                        ->modalSubmitActionLabel('iya, tolak')
                        ->color('danger')
                        ->modalIcon('heroicon-s-chat-bubble-left-ellipsis')
                        ->icon('heroicon-s-document-check')
                        ->modalIconColor('danger')
                        ->form([
                            Forms\Components\Textarea::make('note')
                                ->label(false)
                                ->live(onBlur: true)
                                ->helperText('* Note atau pemberitahuan untuk member')
                        ])
                        ->hidden(function (user_bab_buku_kolaborasi $record) {
                            if ($record->status != 'UPLOADED') {
                                return true;
                            }

                            return false;
                        })
                        ->action(function (user_bab_buku_kolaborasi $record, array $data) {
                            $bab_buku_kolaborasi = $record->bab_buku_kolaborasi;

                            // count diff day created_at and updated_at
                            $diff_day = Carbon::parse($record->created_at)->diffInDays(Carbon::parse($record->updated_at));

                            $final_day = $bab_buku_kolaborasi->durasi_pembuatan - $diff_day;

                            $record->update([
                                'status' => 'REJECTED',
                                'note' => $data['note'],
                                'datetime_deadline' => Carbon::now()->addDays($final_day)->format('Y-m-d H:i:s'),
                            ]);

                            Notification::make()
                                ->success()
                                ->title(
                                    'Kolaborasi Bab ' . $record->user->nama_lengkap . ' berhasil direject/ditolak!'
                                )
                                ->send();

                            $recipientUser = $record->user;

                            // send notif to user yang bayar
                            Notification::make()
                                ->success()
                                ->title(
                                    'Kolaborasi Buku ' . $record->bab_buku_kolaborasi->buku_kolaborasi->judul . ' Anda masih ada yang perlu diperbaiki!'
                                )
                                ->body(
                                    $data['note']
                                )
                                ->sendToDatabase($recipientUser);

                            event(new DatabaseNotificationsSent($recipientUser));
                        }),
                    Tables\Actions\Action::make('Berikan Note')
                        ->icon('heroicon-s-document-check')
                        ->hidden(function (user_bab_buku_kolaborasi $record) {
                            if ($record->status == 'FAILED') {
                                return true;
                            }

                            return false;
                        })
                        ->form([
                            Forms\Components\Textarea::make('note')
                                ->label(false)
                                ->live(onBlur: true)
                                ->helperText('* Note atau pemberitahuan untuk member')
                        ])
                        ->action(function (user_bab_buku_kolaborasi $record, array $data) {
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
                                    'Note Kolaborasi Baru dari Admin'
                                )
                                ->body(
                                    'Ada Pemberitahuan dari admin penerbitan untuk kolaborasi ' . $record->bab_buku_kolaborasi->buku_kolaborasi->judul . '!'
                                )
                                ->sendToDatabase($recipientUser);

                            event(new DatabaseNotificationsSent($recipientUser));
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->hidden(function (user_bab_buku_kolaborasi $record) {
                            if ($record->status != 'PROGRESS' && $record->status != 'FAILED') {
                                return true;
                            }

                            return false;
                        }),
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
            'index' => Pages\ListUserBabBukuKolaborasis::route('/'),
            'create' => Pages\CreateUserBabBukuKolaborasi::route('/create'),
            // 'edit' => Pages\EditUserBabBukuKolaborasi::route('/{record}/edit'),
        ];
    }
}
