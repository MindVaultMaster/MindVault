<?php

namespace App\Filament\User\Resources\JournalEntryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SubstancesRelationManager extends RelationManager
{
    protected static string $relationship = 'substances';

    protected static ?string $title = 'Substances Taken';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('substance_id')
                    ->label('Substance')
                    ->relationship(
                        'substance',
                        'name',
                        fn (Builder $query) => $query->where(function ($q) {
                            $q->where('is_public', true)
                              ->orWhere('created_by_user_id', Auth::id());
                        })
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(2),
                        Forms\Components\Select::make('category')
                            ->options([
                                'nootropic' => 'Nootropic',
                                'medication' => 'Medication',
                                'supplement' => 'Supplement',
                                'vitamin' => 'Vitamin',
                                'mineral' => 'Mineral',
                                'herb' => 'Herb',
                                'other' => 'Other',
                            ]),
                        Forms\Components\TextInput::make('common_dosage')
                            ->label('Common Dosage')
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(function (array $data) {
                        $data['created_by_user_id'] = Auth::id();
                        $data['is_public'] = false;
                        return \App\Models\Substance::create($data)->getKey();
                    }),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('dosage')
                            ->label('Dosage Taken')
                            ->placeholder('e.g., 100mg, 2 pills')
                            ->maxLength(255),

                        Forms\Components\DateTimePicker::make('taken_at')
                            ->label('Time Taken')
                            ->default(now())
                            ->seconds(false),
                    ]),

                Forms\Components\TextInput::make('duration_minutes')
                    ->label('Duration (minutes)')
                    ->numeric()
                    ->placeholder('How long did effects last?'),

                Forms\Components\Fieldset::make('Effect Ratings')
                    ->schema([
                        Forms\Components\Select::make('focus_rating')
                            ->label('Focus')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ])
                            ->placeholder('Rate focus effects'),

                        Forms\Components\Select::make('mood_rating')
                            ->label('Mood')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ])
                            ->placeholder('Rate mood effects'),

                        Forms\Components\Select::make('sleep_rating')
                            ->label('Sleep')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ])
                            ->placeholder('Rate sleep effects'),

                        Forms\Components\Select::make('effectiveness_rating')
                            ->label('Overall Effectiveness')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ])
                            ->placeholder('Rate overall effectiveness'),
                    ])
                    ->columns(2),

                Forms\Components\Textarea::make('side_effects')
                    ->label('Side Effects')
                    ->placeholder('Any negative effects experienced?')
                    ->rows(2),

                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->placeholder('Additional notes about this substance')
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Substance')
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'nootropic' => 'info',
                        'medication' => 'warning',
                        'supplement' => 'success',
                        'vitamin' => 'success',
                        'mineral' => 'success',
                        'herb' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('pivot.dosage')
                    ->label('Dosage')
                    ->placeholder('Not specified'),

                Tables\Columns\TextColumn::make('pivot.taken_at')
                    ->label('Time Taken')
                    ->dateTime('M j, g:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pivot.focus_rating')
                    ->label('Focus')
                    ->badge()
                    ->color(fn (?int $state): string => match ($state) {
                        1, 2 => 'danger',
                        3 => 'warning',
                        4, 5 => 'success',
                        default => 'gray',
                    })
                    ->placeholder('N/R'),

                Tables\Columns\TextColumn::make('pivot.mood_rating')
                    ->label('Mood')
                    ->badge()
                    ->color(fn (?int $state): string => match ($state) {
                        1, 2 => 'danger',
                        3 => 'warning',
                        4, 5 => 'success',
                        default => 'gray',
                    })
                    ->placeholder('N/R'),

                Tables\Columns\TextColumn::make('pivot.effectiveness_rating')
                    ->label('Effectiveness')
                    ->badge()
                    ->color(fn (?int $state): string => match ($state) {
                        1, 2 => 'danger',
                        3 => 'warning',
                        4, 5 => 'success',
                        default => 'gray',
                    })
                    ->placeholder('N/R'),

                Tables\Columns\TextColumn::make('pivot.duration_minutes')
                    ->label('Duration')
                    ->suffix(' min')
                    ->placeholder('N/R'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('substance', 'category')
                    ->options([
                        'nootropic' => 'Nootropic',
                        'medication' => 'Medication',
                        'supplement' => 'Supplement',
                        'vitamin' => 'Vitamin',
                        'mineral' => 'Mineral',
                        'herb' => 'Herb',
                        'other' => 'Other',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Substance')
                    ->mutateFormDataUsing(function (array $data): array {
                        $substanceId = $data['substance_id'];
                        unset($data['substance_id']);
                        $data['substance_id'] = $substanceId;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])
            ->defaultSort('pivot.taken_at', 'desc');
    }
}