<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\FeedbackResource\Pages;
use App\Models\Feedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Feedback';

    protected static ?string $navigationGroup = '🧠 MindVault';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Feedback Type')
                    ->options([
                        'general' => 'General Feedback',
                        'bug' => 'Bug Report',
                        'feature' => 'Feature Request',
                        'improvement' => 'Improvement Suggestion',
                    ])
                    ->default('general')
                    ->required(),

                Forms\Components\TextInput::make('subject')
                    ->label('Subject')
                    ->placeholder('Brief description of your feedback')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('message')
                    ->label('Message')
                    ->placeholder('Please provide detailed feedback...')
                    ->required()
                    ->rows(6)
                    ->maxLength(2000),

                Forms\Components\Select::make('priority')
                    ->label('Priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ])
                    ->default('medium')
                    ->required(),

                // Show admin response if it exists (read-only)
                Forms\Components\Textarea::make('admin_response')
                    ->label('Admin Response')
                    ->disabled()
                    ->visible(fn ($record) => $record && $record->admin_response)
                    ->rows(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                    ->limit(50),

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
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->status === 'open'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
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