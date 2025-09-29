<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\SubstanceResource\Pages;
use App\Models\Substance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SubstanceResource extends Resource
{
    protected static ?string $model = Substance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Nootropics';

    protected static ?string $navigationGroup = '📊 Tracking';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where(function ($query) {
            $query->where('is_public', true)
                  ->orWhere('created_by_user_id', Auth::id());
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Modafinil, L-Theanine'),

                Forms\Components\Textarea::make('description')
                    ->placeholder('What is this substance? What does it do?')
                    ->rows(3)
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
                    ])
                    ->placeholder('Select category'),

                Forms\Components\TextInput::make('common_dosage')
                    ->label('Common Dosage')
                    ->placeholder('e.g., 100-200mg, 1-2 pills')
                    ->maxLength(255),

                Forms\Components\Textarea::make('notes')
                    ->label('Personal Notes')
                    ->placeholder('Your personal notes about this substance')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_public')
                    ->label('Share with Community')
                    ->helperText('Allow other users to see and use this substance')
                    ->default(false)
                    ->hidden(fn (?Substance $record) => $record?->is_predefined),
            ])
            ->columns(1);
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
                    ->label('Official')
                    ->boolean()
                    ->tooltip('Official substance available to all users'),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Shared')
                    ->boolean()
                    ->tooltip('Shared with community'),

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
                    ->url(fn (Substance $record): string => '/user/research-library?substances[]=' . $record->id)
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
                    ->label('Official Substances')
                    ->placeholder('All substances')
                    ->trueLabel('Official only')
                    ->falseLabel('Personal only'),

                Tables\Filters\TernaryFilter::make('created_by_me')
                    ->label('Created by me')
                    ->placeholder('All substances')
                    ->trueLabel('My substances')
                    ->falseLabel('Others\' substances')
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === true) {
                            $query->where('created_by_user_id', Auth::id());
                        } elseif ($data['value'] === false) {
                            $query->where(function ($q) {
                                $q->where('created_by_user_id', '!=', Auth::id())
                                  ->orWhereNull('created_by_user_id');
                            });
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Substance $record): bool =>
                        $record->created_by_user_id === Auth::id()
                    ),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Substance $record): bool =>
                        $record->created_by_user_id === Auth::id() && !$record->is_predefined
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => static::getEloquentQuery()
                            ->where('created_by_user_id', Auth::id())
                            ->where('is_predefined', false)
                            ->exists()
                        ),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubstances::route('/'),
            'create' => Pages\CreateSubstance::route('/create'),
            'view' => Pages\ViewSubstance::route('/{record}'),
            'edit' => Pages\EditSubstance::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('created_by_user_id', Auth::id())->count();
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return $record->created_by_user_id === Auth::id();
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return $record->created_by_user_id === Auth::id() && !$record->is_predefined;
    }
}
