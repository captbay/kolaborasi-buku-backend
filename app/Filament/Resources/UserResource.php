<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

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
                    ->maxDate('today')
                    ->required(),
                Forms\Components\Select::make('gender')
                    ->options([
                        'Pria' => 'Pria',
                        'Wanita' => 'Wanita',
                        'Memilih Untuk Tidak Diisi' => 'Memilih Untuk Tidak Diisi',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('alamat')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('provinsi')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kecamatan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kota')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kode_pos')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('role')
                    ->options([
                        'ADMIN' => 'ADMIN',
                        'CUSTOMER' => 'CUSTOMER',
                        'MEMBER' => 'MEMBER',
                    ])
                    ->live()
                    ->required(),
                Forms\Components\FileUpload::make('foto_profil')
                    ->image()
                    ->directory('foto_profil')
                    ->required(),
                Forms\Components\Textarea::make('bio')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('file_cv')
                    ->directory('file_cv'),
                Forms\Components\FileUpload::make('file_ktp')
                    ->image()
                    ->directory('file_ktp'),
                Forms\Components\FileUpload::make('file_ttd')
                    ->image()
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
                Tables\Columns\TextColumn::make('nama_depan')
                    ->label('Nama Lengkap')
                    ->formatStateUsing(function ($state, User $user) {
                        return $user->nama_depan . ' ' . $user->nama_belakang;
                    })
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
                    ->sortable()
                    ->searchable(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->query(function (User $query) {
                return $query->whereNot('role', auth()->user()->role);
            })
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
