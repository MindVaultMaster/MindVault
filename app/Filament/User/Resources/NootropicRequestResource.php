<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\NootropicRequestResource\Pages;
use App\Models\Substance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class NootropicRequestResource extends Resource
{
    protected static ?string $model = Substance::class;

    protected static ?string $navigationIcon = 'heroicon-o-share';

    protected static ?string $navigationLabel = 'Nootropic Requests';

    protected static ?string $navigationGroup = '=, Community';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('created_by_user_id', Auth::id())
            ->where('is_public', true)
            ->where('is_predefined', false);
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
                    ->placeholder('What is this nootropic? What does it do?')
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
                    ->placeholder('Select category')
                    ->required(),

                Forms\Components\TextInput::make('common_dosage')
                    ->label('Common Dosage')
                    ->placeholder('e.g., 100-200mg, 1-2 pills')
                    ->maxLength(255),

                Forms\Components\Textarea::make('notes')
                    ->label('Personal Notes')
                    ->placeholder('Your personal notes about this nootropic')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('is_public')
                    ->default(true),

                Forms\Components\Hidden::make('is_predefined')
                    ->default(false),

                Forms\Components\Hidden::make('created_by_user_id')
                    ->default(Auth::id()),
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

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Shared')
                    ->boolean()
                    ->tooltip('Shared with community'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Submitted'),

                Tables\Columns\TextColumn::make('resources_count')
                    ->label('Research')
                    ->counts('resources')
                    ->badge()
                    ->color('info')
                    ->tooltip('Research articles linked'),
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
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No Nootropic Requests')
            ->emptyStateDescription('You haven\'t shared any nootropics with the community yet.')
            ->emptyStateIcon('heroicon-o-share');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNootropicRequests::route('/'),
            'create' => Pages\CreateNootropicRequest::route('/create'),
            'view' => Pages\ViewNootropicRequest::route('/{record}'),
            'edit' => Pages\EditNootropicRequest::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->count();
        return $count > 0 ? (string) $count : null;
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

    public static function getModelLabel(): string
    {
        return 'Nootropic Request';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Nootropic Requests';
    }
}