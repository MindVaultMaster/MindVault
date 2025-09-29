<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\NoteResource\Pages;
use App\Models\Note;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class NoteResource extends Resource
{
    protected static ?string $model = Note::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Notes';

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
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Note title'),

                Forms\Components\RichEditor::make('content')
                    ->required()
                    ->placeholder('Write your notes here...')
                    ->columnSpanFull(),

                Forms\Components\TagsInput::make('tags')
                    ->placeholder('Add tags to organize your notes'),

                Forms\Components\Toggle::make('is_pinned')
                    ->label('Pin this note')
                    ->helperText('Pinned notes appear at the top'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_pinned')
                    ->label('📌')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('content')
                    ->html()
                    ->limit(50)
                    ->placeholder('No content'),

                Tables\Columns\TextColumn::make('tags')
                    ->badge()
                    ->separator(',')
                    ->placeholder('No tags'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->label('Created'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->label('Updated')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_pinned')
                    ->label('Pinned Notes')
                    ->placeholder('All notes')
                    ->trueLabel('Pinned only')
                    ->falseLabel('Unpinned only'),
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
            ->defaultSort('is_pinned', 'desc')
            ->defaultSort('updated_at', 'desc')
            ->emptyStateHeading('No Notes Yet')
            ->emptyStateDescription('Start taking notes to organize your thoughts and research.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotes::route('/'),
            'create' => Pages\CreateNote::route('/create'),
            'view' => Pages\ViewNote::route('/{record}'),
            'edit' => Pages\EditNote::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_id', Auth::id())->count();
    }
}
