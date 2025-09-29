<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalEntryResource\Pages;
use App\Filament\Resources\JournalEntryResource\RelationManagers;
use App\Models\JournalEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Journal Entries';

    protected static ?string $navigationGroup = 'Nootropics Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('title')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('entry_date')
                    ->required()
                    ->default(now()),
                Forms\Components\TimePicker::make('entry_time'),
                Forms\Components\Textarea::make('content')
                    ->label('General Journal Content')
                    ->columnSpanFull(),

                Forms\Components\Section::make('Overall Daily Ratings')
                    ->description('Rate your overall day (1-5 scale)')
                    ->schema([
                        Forms\Components\Select::make('overall_focus')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ]),
                        Forms\Components\Select::make('overall_mood')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ]),
                        Forms\Components\Select::make('overall_sleep')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ]),
                        Forms\Components\Select::make('overall_energy')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Textarea::make('general_notes')
                    ->label('General Notes')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_public')
                    ->label('Share with Community')
                    ->helperText('Allow other users to see this entry anonymously'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('substances.name')
                    ->label('Substances')
                    ->badge()
                    ->color('info')
                    ->separator(', ')
                    ->limit(2)
                    ->placeholder('None'),
                Tables\Columns\TextColumn::make('entry_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('entry_time')
                    ->time(),
                Tables\Columns\TextColumn::make('overall_focus')
                    ->label('Focus')
                    ->badge()
                    ->color(fn (?int $state): string => match ($state) {
                        1, 2 => 'danger',
                        3 => 'warning',
                        4, 5 => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('overall_mood')
                    ->label('Mood')
                    ->badge()
                    ->color(fn (?int $state): string => match ($state) {
                        1, 2 => 'danger',
                        3 => 'warning',
                        4, 5 => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('overall_sleep')
                    ->label('Sleep')
                    ->badge()
                    ->color(fn (?int $state): string => match ($state) {
                        1, 2 => 'danger',
                        3 => 'warning',
                        4, 5 => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SubstancesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournalEntries::route('/'),
            'create' => Pages\CreateJournalEntry::route('/create'),
            'edit' => Pages\EditJournalEntry::route('/{record}/edit'),
        ];
    }
}
