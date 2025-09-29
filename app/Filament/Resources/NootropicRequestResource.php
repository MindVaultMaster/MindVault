<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NootropicRequestResource\Pages;
use App\Models\Substance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NootropicRequestResource extends Resource
{
    protected static ?string $model = Substance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Medication Requests';

    protected static ?string $navigationGroup = 'Nootropics Management';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('created_by_user_id')
            ->where('is_predefined', false);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Nootropic Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
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
                            ->required(),

                        Forms\Components\TextInput::make('common_dosage')
                            ->label('Common Dosage')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('notes')
                            ->label('User Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Admin Actions')
                    ->schema([
                        Forms\Components\Toggle::make('is_public')
                            ->label('Public Visibility')
                            ->helperText('Allow other users to see and use this nootropic'),

                        Forms\Components\Toggle::make('is_predefined')
                            ->label('Make Official')
                            ->helperText('Make this an official nootropic available to all users'),

                        Forms\Components\Select::make('created_by_user_id')
                            ->relationship('createdBy', 'name')
                            ->label('Submitted By')
                            ->disabled()
                            ->dehydrated(false),
                    ]),
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
                    ->sortable(),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Submitted By')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 40) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('common_dosage')
                    ->label('Dosage')
                    ->placeholder('Not specified'),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean()
                    ->tooltip('Visible to community'),

                Tables\Columns\IconColumn::make('is_predefined')
                    ->label('Official')
                    ->boolean()
                    ->tooltip('Official nootropic'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Submitted'),
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

                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Public Visibility')
                    ->placeholder('All requests')
                    ->trueLabel('Public only')
                    ->falseLabel('Private only'),

                Tables\Filters\TernaryFilter::make('is_predefined')
                    ->label('Approval Status')
                    ->placeholder('All requests')
                    ->trueLabel('Approved only')
                    ->falseLabel('Pending approval'),

                Tables\Filters\Filter::make('pending_approval')
                    ->query(fn (Builder $query): Builder => $query->where('is_predefined', false))
                    ->label('Pending Approval')
                    ->toggle()
                    ->default(true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Substance $record): bool => !$record->is_predefined)
                    ->requiresConfirmation()
                    ->modalHeading('Approve Nootropic Request')
                    ->modalDescription('This will approve this user-submitted nootropic and make it available as an official substance for all users.')
                    ->action(function (Substance $record) {
                        $record->update([
                            'is_predefined' => true,
                            'is_public' => true,
                        ]);
                    })
                    ->successNotificationTitle('Nootropic request approved successfully'),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Substance $record): bool => !$record->is_predefined)
                    ->requiresConfirmation()
                    ->modalHeading('Reject Nootropic Request')
                    ->modalDescription('This will delete this user-submitted nootropic request. This action cannot be undone.')
                    ->action(function (Substance $record) {
                        $record->delete();
                    })
                    ->successNotificationTitle('Nootropic request rejected and removed'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Selected Requests')
                        ->modalDescription('This will approve all selected nootropic requests and make them available as official substances.')
                        ->action(function ($records) {
                            $records->each(function (Substance $record) {
                                if (!$record->is_predefined) {
                                    $record->update([
                                        'is_predefined' => true,
                                        'is_public' => true,
                                    ]);
                                }
                            });
                        })
                        ->successNotificationTitle('Selected requests approved successfully'),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Reject Selected'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No Medication Requests')
            ->emptyStateDescription('No user-submitted nootropics found.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNootropicRequests::route('/'),
            'view' => Pages\ViewNootropicRequest::route('/{record}'),
            'edit' => Pages\EditNootropicRequest::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->where('is_predefined', false)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function canCreate(): bool
    {
        return false; // Admins don't create requests, users do
    }

    public static function getModelLabel(): string
    {
        return 'Medication Request';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Medication Requests';
    }
}