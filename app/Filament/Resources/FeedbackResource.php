<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages;
use App\Models\Feedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'User Feedback';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Feedback Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Select::make('type')
                            ->options([
                                'general' => 'General Feedback',
                                'bug' => 'Bug Report',
                                'feature' => 'Feature Request',
                                'improvement' => 'Improvement Suggestion',
                            ])
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('subject')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Textarea::make('message')
                            ->label('User Message')
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(4),

                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                            ])
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Admin Actions')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'open' => 'Open',
                                'in_progress' => 'In Progress',
                                'resolved' => 'Resolved',
                                'closed' => 'Closed',
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('admin_response')
                            ->label('Admin Response')
                            ->placeholder('Provide a response to the user...')
                            ->rows(6)
                            ->maxLength(2000),

                        Forms\Components\DateTimePicker::make('responded_at')
                            ->label('Response Date')
                            ->disabled()
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bug' => 'danger',
                        'feature' => 'success',
                        'improvement' => 'info',
                        'general' => 'gray',
                    }),

                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'gray',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        'closed' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'warning',
                        'high' => 'danger',
                    }),

                Tables\Columns\IconColumn::make('has_response')
                    ->label('Responded')
                    ->getStateUsing(fn ($record) => !is_null($record->admin_response))
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'general' => 'General',
                        'bug' => 'Bug Report',
                        'feature' => 'Feature Request',
                        'improvement' => 'Improvement',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ]),

                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ]),

                Tables\Filters\Filter::make('unresponded')
                    ->query(fn (Builder $query): Builder => $query->whereNull('admin_response'))
                    ->label('Unresponded'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListFeedback::route('/'),
            'create' => Pages\CreateFeedback::route('/create'),
            'view' => Pages\ViewFeedback::route('/{record}'),
            'edit' => Pages\EditFeedback::route('/{record}/edit'),
        ];
    }
}