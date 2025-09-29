<?php

namespace App\Filament\Resources\JournalEntryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubstancesRelationManager extends RelationManager
{
    protected static string $relationship = 'substances';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('substance_id')
                    ->relationship('substance', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
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
                            ->maxLength(255),
                    ]),

                Forms\Components\TextInput::make('dosage')
                    ->label('Dosage Taken')
                    ->placeholder('e.g., 100mg, 2 pills'),

                Forms\Components\DateTimePicker::make('taken_at')
                    ->label('Time Taken')
                    ->default(now()),

                Forms\Components\TextInput::make('duration_minutes')
                    ->label('Duration (minutes)')
                    ->numeric()
                    ->placeholder('How long did effects last?'),

                Forms\Components\Section::make('Effect Ratings (1-5)')
                    ->schema([
                        Forms\Components\Select::make('focus_rating')
                            ->label('Focus Rating')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ]),
                        Forms\Components\Select::make('mood_rating')
                            ->label('Mood Rating')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ]),
                        Forms\Components\Select::make('sleep_rating')
                            ->label('Sleep Rating')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ]),
                        Forms\Components\Select::make('effectiveness_rating')
                            ->label('Overall Effectiveness')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Textarea::make('side_effects')
                    ->label('Side Effects')
                    ->placeholder('Any negative effects experienced?'),

                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->placeholder('Additional notes about this substance'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Substance'),
                Tables\Columns\TextColumn::make('pivot.dosage')
                    ->label('Dosage'),
                Tables\Columns\TextColumn::make('pivot.taken_at')
                    ->label('Time Taken')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('pivot.focus_rating')
                    ->label('Focus')
                    ->badge()
                    ->color(fn (?int $state): string => match ($state) {
                        1, 2 => 'danger',
                        3 => 'warning',
                        4, 5 => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('pivot.mood_rating')
                    ->label('Mood')
                    ->badge()
                    ->color(fn (?int $state): string => match ($state) {
                        1, 2 => 'danger',
                        3 => 'warning',
                        4, 5 => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('pivot.effectiveness_rating')
                    ->label('Effectiveness')
                    ->badge()
                    ->color(fn (?int $state): string => match ($state) {
                        1, 2 => 'danger',
                        3 => 'warning',
                        4, 5 => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('pivot.duration_minutes')
                    ->label('Duration (min)'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Remove substance_id from pivot data
                        $substanceId = $data['substance_id'];
                        unset($data['substance_id']);
                        $data['substance_id'] = $substanceId;
                        return $data;
                    }),
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('dosage')->label('Dosage'),
                        Forms\Components\DateTimePicker::make('taken_at')->default(now()),
                        Forms\Components\Select::make('focus_rating')->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),
                        Forms\Components\Select::make('mood_rating')->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),
                        Forms\Components\Select::make('effectiveness_rating')->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}