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
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                                $query->whereDoesntHave('transaksi_kolaborasi_buku', function ($query) {
                                    $query->whereNot('date_time_lunas', null);
                                })->whereDoesntHave('user_bab_buku_kolaborasi',  function ($query) {
                                    $query->whereNot('status', 'REJECTED');
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
                            ->helperText('* Bab yang diambil hanya bab yang belum dibayar lunas ataupun belum diassign ke user')
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
                                'UPLOADED' => 'Uploaded',
                                'REVISI' => 'Revisi',
                                'DONE' => 'Done',
                                'REJECTED' => 'Rejected',
                            ])
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.nama_depan')
                    ->label('Nama Lengkap')
                    ->formatStateUsing(function ($state, user_bab_buku_kolaborasi $user) {
                        return $user->user->nama_depan . ' ' . $user->user->nama_belakang;
                    })
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
                            'REVISI' => 'warning',
                            'DONE' => 'success',
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
            'index' => Pages\ListUserBabBukuKolaborasis::route('/'),
            'create' => Pages\CreateUserBabBukuKolaborasi::route('/create'),
            'edit' => Pages\EditUserBabBukuKolaborasi::route('/{record}/edit'),
        ];
    }
}
