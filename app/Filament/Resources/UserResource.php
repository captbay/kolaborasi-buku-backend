<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_depan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_belakang')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(User::class, 'email', ignoreRecord: true),
                Forms\Components\TextInput::make('no_telepon')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tgl_lahir')
                    ->native(false)
                    ->maxDate('today'),
                Forms\Components\Select::make('gender')
                    ->options([
                        'Pria' => 'Pria',
                        'Wanita' => 'Wanita',
                        'Memilih Untuk Tidak Diisi' => 'Memilih Untuk Tidak Diisi',
                    ]),
                Forms\Components\TextInput::make('alamat')
                    ->maxLength(255),
                Forms\Components\TextInput::make('provinsi')
                    ->maxLength(255),
                Forms\Components\TextInput::make('kecamatan')
                    ->maxLength(255),
                Forms\Components\TextInput::make('kota')
                    ->maxLength(255),
                Forms\Components\TextInput::make('kode_pos')
                    ->maxLength(255),
                Forms\Components\Select::make('role')
                    ->options([
                        'CUSTOMER' => 'CUSTOMER',
                        'MEMBER' => 'MEMBER',
                    ])
                    ->live()
                    ->disabled()
                    ->required(),
                Forms\Components\FileUpload::make('foto_profil')
                    ->image()
                    ->imageEditor()
                    ->openable()
                    ->downloadable()
                    ->directory('foto_profil'),
                Forms\Components\FileUpload::make('file_cv')
                    ->openable()
                    ->downloadable()
                    ->directory('file_cv'),
                Forms\Components\FileUpload::make('file_ktp')
                    ->openable()
                    ->downloadable()
                    ->image()
                    ->imageEditor()
                    ->directory('file_ktp'),
                Forms\Components\FileUpload::make('file_ttd')
                    ->openable()
                    ->downloadable()
                    ->image()
                    ->imageEditor()
                    ->directory('file_ttd'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // show profile picture
                Tables\Columns\ImageColumn::make('foto_profil')
                    ->label('Foto Profil'),
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(
                        fn (User $user) => match ($user->role) {
                            'ADMIN' => 'primary',
                            'CUSTOMER' => 'warning',
                            'MEMBER' => 'success',
                        }
                    )
                    ->sortable()
                    ->searchable(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->slideOver(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\Action::make('Verifikasi')
                        ->hidden(function (User $user, array $data) {
                            if ($user->deleted_at != null) {
                                return true;
                            }

                            if ($user->role == 'MEMBER') {
                                return true;
                            }

                            if ($user->role == 'ADMIN') {
                                return true;
                            }

                            return false;
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Verifikasi Customer')
                        ->modalDescription('Apakah anda yakin ingin melakukan verifikasi customer ini, agar menjadi member?')
                        ->modalSubmitActionLabel('iya, verifikasi')
                        ->color('success')
                        ->modalIcon('heroicon-s-chat-bubble-left-ellipsis')
                        ->icon('heroicon-s-document-check')
                        ->modalIconColor('success')
                        ->action(
                            function (user $user, array $data): void {
                                if ($user->role == 'MEMBER') {
                                    Notification::make()
                                        ->danger()
                                        ->title('Customer ini sudah menjadi member')
                                        ->send();

                                    return;
                                }

                                if ($user->file_cv == null || $user->file_ktp == null || $user->file_ttd == null) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Customer ini belum melengkapi data')
                                        ->send();

                                    return;
                                }

                                //  update role to member
                                $user->update([
                                    'role' => 'MEMBER',
                                ]);

                                $recipientAdmin = auth()->user();

                                Notification::make()
                                    ->success()
                                    ->title('Customer ' . $user->nama_lengkap . ' berhasil diverifikasi menjadi member')
                                    ->sendToDatabase($recipientAdmin)
                                    ->send();

                                // send notif to user
                                Notification::make()
                                    ->success()
                                    ->title(
                                        'Verifikasi Member Berhasil'
                                    )
                                    ->body(
                                        'Selamat, Anda telah berhasil diverifikasi menjadi member. Anda dapat melakukan kolaborasi sekarang.'
                                    )
                                    ->sendToDatabase($user);

                                return;
                            }
                        ),
                ])->iconButton()
            ])
            ->query(function (User $query) {
                return $query->whereNot('role', auth()->user()->role);
            })
            ->recordUrl(false)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
