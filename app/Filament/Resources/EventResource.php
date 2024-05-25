<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use App\Models\konten_event;
use App\Tables\Columns\VideoColumn;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = konten_event::class;

    protected static ?string $navigationIcon = 'heroicon-s-photo';

    protected static ?string $navigationLabel = 'Galeri Event';

    protected static ?string $label = 'Galeri Event';

    protected static ?string $slug = 'galeri-event';

    protected static ?string $title = 'Galeri Event';

    protected static ?int $navigationSort = 4;

    // navigation groups

    protected static ?string $navigationGroup = 'Setting Web Customer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Tipe')
                    ->schema([
                        Forms\Components\Select::make('tipe')
                            ->label(false)
                            ->options([
                                'IMAGE' => 'Gambar',
                                // 'VIDEO' => 'Video',
                            ])
                            ->default('IMAGE')
                            ->live(onBlur: true)
                            ->required(),
                    ]),

                Forms\Components\Section::make('File')
                    ->schema([
                        // Forms\Components\FileUpload::make('file')
                        //     ->label('video')
                        //     ->openable()
                        //     ->required()
                        //     ->acceptedFileTypes(['video/mp4'])
                        //     ->preserveFilenames()
                        //     ->maxSize(12288)
                        //     ->hidden(fn (Forms\Get $get) => $get('tipe') !== 'VIDEO')
                        //     ->directory('galeri_config_file/video'),

                        Forms\Components\FileUpload::make('file')
                            ->label('Gambar')
                            ->required()
                            ->openable()
                            ->image()
                            ->imageEditor()
                            ->helperText('Gambar direkomendasikan berukuran 1440 px x 384 px')
                            ->hidden(fn (Forms\Get $get) => $get('tipe') !== 'IMAGE')
                            ->directory('galeri_config_file/image'),
                    ])->description('Tipe file'),

                Forms\Components\Section::make('Tanggal Waktu Mulai')
                    ->schema([
                        Forms\Components\DateTimePicker::make('waktu_mulai')
                            ->label(false)
                            ->before('waktu_selesai')
                            ->validationMessages([
                                'before' => ':attribute harus sebelum tanggal waktu Selesai',
                            ])
                            ->required(),
                    ]),

                Forms\Components\Section::make('Tanggal Waktu Selesai')
                    ->schema([
                        Forms\Components\DateTimePicker::make('waktu_selesai')
                            ->label(false)
                            ->after('waktu_mulai')
                            ->validationMessages([
                                'after' => ':attribute harus sesudah tanggal waktu mulai',
                            ])
                            ->required(),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('active_flag')
                            ->label('Dipublish atau tidak')
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipe')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('waktu_mulai')
                    ->label('Tanggal Waktu Mulai')
                    ->formatStateUsing(function (konten_event $event) {
                        return Carbon::parse($event->waktu_mulai)->format('d F Y // H:i');
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('waktu_selesai')
                    ->label('Tanggal Waktu Selesai')
                    ->formatStateUsing(function (konten_event $event) {
                        return Carbon::parse($event->waktu_selesai)->format('d F Y // H:i');
                    })
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
