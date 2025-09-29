<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\JournalEntryResource\Pages;
use App\Filament\User\Resources\JournalEntryResource\RelationManagers;
use App\Models\JournalEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Journal';

    protected static ?string $navigationGroup = '📊 Tracking';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->maxLength(255)
                    ->placeholder('Optional title for this entry'),

                Forms\Components\DatePicker::make('entry_date')
                    ->required()
                    ->default(now())
                    ->label('Date'),

                Forms\Components\TimePicker::make('entry_time')
                    ->label('Time')
                    ->default(now()),

                Forms\Components\Textarea::make('content')
                    ->label('Journal Entry')
                    ->placeholder('How was your day? What did you experience?')
                    ->rows(4)
                    ->columnSpanFull(),

                Forms\Components\Select::make('substance_tags')
                    ->label('Substances Taken')
                    ->multiple()
                    ->options(function () {
                        return \App\Models\Substance::where(function ($query) {
                            $query->where('is_public', true)
                                  ->orWhere('created_by_user_id', Auth::id());
                        })->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->placeholder('Select substances you took today')
                    ->helperText('Tag the substances you took to link them to this journal entry')
                    ->columnSpanFull(),

                Forms\Components\Section::make('Daily Ratings')
                    ->description('Rate your overall day (1-5 scale)')
                    ->schema([
                        Forms\Components\Select::make('overall_focus')
                            ->label('Focus')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ])
                            ->placeholder('Rate your focus'),

                        Forms\Components\Select::make('overall_mood')
                            ->label('Mood')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ])
                            ->placeholder('Rate your mood'),

                        Forms\Components\Select::make('overall_sleep')
                            ->label('Sleep Quality')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ])
                            ->placeholder('Rate your sleep'),

                        Forms\Components\Select::make('overall_energy')
                            ->label('Energy Level')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ])
                            ->placeholder('Rate your energy'),
                    ])
                    ->columns(2),

                Forms\Components\Textarea::make('general_notes')
                    ->label('Additional Notes')
                    ->placeholder('Any other observations or notes?')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_public')
                    ->label('Share with Community')
                    ->helperText('Allow other users to see this entry anonymously for research purposes')
                    ->default(false),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entry_date')
                    ->label('Date')
                    ->date()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30)
                    ->placeholder('No title'),

                Tables\Columns\TextColumn::make('substances.name')
                    ->label('Substances')
                    ->badge()
                    ->color('info')
                    ->separator(', ')
                    ->limit(3)
                    ->tooltip(function ($record) {
                        $substances = $record->substances()->pluck('name')->toArray();
                        return implode(', ', $substances);
                    })
                    ->placeholder('No substances'),

                Tables\Columns\TextColumn::make('entry_time')
                    ->label('Time')
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
                    ->label('Shared')
                    ->boolean(),

                Tables\Columns\TextColumn::make('substances_count')
                    ->label('Substances')
                    ->counts('substances')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('overall_focus')
                    ->options([
                        1 => '1 - Very Poor',
                        2 => '2 - Poor',
                        3 => '3 - Average',
                        4 => '4 - Good',
                        5 => '5 - Excellent',
                    ]),
                Tables\Filters\Filter::make('entry_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('entry_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('entry_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('entry_date', 'desc');
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
            'view' => Pages\ViewJournalEntry::route('/{record}'),
            'edit' => Pages\EditJournalEntry::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_id', Auth::id())->count();
    }
}
