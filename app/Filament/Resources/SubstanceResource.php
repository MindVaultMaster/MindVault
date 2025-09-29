<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubstanceResource\Pages;
use App\Filament\Resources\SubstanceResource\RelationManagers;
use App\Models\Substance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubstanceResource extends Resource
{
    protected static ?string $model = Substance::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationLabel = 'Nootropics';

    protected static ?string $navigationGroup = 'Nootropics Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
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
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_predefined')
                    ->label('Predefined Substance')
                    ->helperText('Admin-created substances available to all users'),
                Forms\Components\Toggle::make('is_public')
                    ->label('Public')
                    ->helperText('Allow other users to see and use this substance'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'nootropic' => 'info',
                        'medication' => 'warning',
                        'supplement' => 'success',
                        'vitamin' => 'success',
                        'mineral' => 'success',
                        'herb' => 'info',
                        default => 'gray',
                    })
                    ->placeholder('Uncategorized')
                    ->sortable(),

                Tables\Columns\TextColumn::make('common_dosage')
                    ->label('Common Dosage')
                    ->placeholder('Not specified'),

                Tables\Columns\IconColumn::make('is_predefined')
                    ->label('Predefined')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean(),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->placeholder('System')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('resources_count')
                    ->label('Research')
                    ->counts('resources')
                    ->badge()
                    ->color('info')
                    ->url(fn (Substance $record): string => static::getUrl('edit', ['record' => $record]) . '#resources')
                    ->tooltip('Click to view research articles'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'nootropic' => 'Nootropic',
                        'medication' => 'Medication',
                        'supplement' => 'Supplement',
                        'vitamin' => 'Vitamin',
                        'mineral' => 'Mineral',
                        'herb' => 'Herb',
                        'other' => 'Other',
                    ]),

                Tables\Filters\TernaryFilter::make('is_predefined')
                    ->label('Predefined Substances')
                    ->placeholder('All substances')
                    ->trueLabel('Predefined only')
                    ->falseLabel('User submissions only'),

                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Public Visibility')
                    ->placeholder('All substances')
                    ->trueLabel('Public only')
                    ->falseLabel('Private only'),

                Tables\Filters\Filter::make('user_submissions')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('created_by_user_id')->where('is_predefined', false))
                    ->label('User Submissions')
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('approve')
                    ->label('Make Official')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Substance $record): bool => !$record->is_predefined && $record->created_by_user_id !== null)
                    ->requiresConfirmation()
                    ->modalHeading('Make Official Nootropic')
                    ->modalDescription('This will make this user-submitted nootropic available as an official substance for all users.')
                    ->action(function (Substance $record) {
                        $record->update([
                            'is_predefined' => true,
                            'is_public' => true,
                        ]);
                    })
                    ->successNotificationTitle('Nootropic approved and made official'),
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
            RelationManagers\ResourcesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubstances::route('/'),
            'create' => Pages\CreateSubstance::route('/create'),
            'edit' => Pages\EditSubstance::route('/{record}/edit'),
        ];
    }
}
