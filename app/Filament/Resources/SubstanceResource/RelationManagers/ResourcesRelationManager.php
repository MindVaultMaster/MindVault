<?php

namespace App\Filament\Resources\SubstanceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ResourcesRelationManager extends RelationManager
{
    protected static string $relationship = 'resources';

    protected static ?string $title = 'Research Library';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->rows(3),

                Forms\Components\Select::make('type')
                    ->options([
                        'study' => 'Research Study',
                        'article' => 'Article',
                        'review' => 'Review/Meta-analysis',
                        'book' => 'Book',
                        'video' => 'Video',
                        'website' => 'Website',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('url')
                    ->label('URL')
                    ->url()
                    ->maxLength(2048),

                Forms\Components\TextInput::make('authors')
                    ->maxLength(255),

                Forms\Components\TextInput::make('publication')
                    ->label('Journal/Publication')
                    ->maxLength(255),

                Forms\Components\DatePicker::make('publication_date'),

                Forms\Components\TextInput::make('doi')
                    ->label('DOI')
                    ->maxLength(255)
                    ->placeholder('10.1000/123456'),

                Forms\Components\Select::make('quality_score')
                    ->label('Quality Score (1-10)')
                    ->options([
                        1 => '1 - Very Poor', 2 => '2 - Poor', 3 => '3 - Below Average',
                        4 => '4 - Fair', 5 => '5 - Average', 6 => '6 - Above Average',
                        7 => '7 - Good', 8 => '8 - Very Good', 9 => '9 - Excellent',
                        10 => '10 - Outstanding',
                    ]),

                Forms\Components\Textarea::make('abstract')
                    ->rows(4),

                Forms\Components\TagsInput::make('tags')
                    ->placeholder('Add tags like: memory, focus, safety, dosage'),

                Forms\Components\Textarea::make('key_findings')
                    ->label('Key Findings/Summary')
                    ->rows(4),

                Forms\Components\Toggle::make('is_verified')
                    ->label('Verified')
                    ->helperText('Mark as verified by administrators'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'study' => 'success',
                        'article' => 'info',
                        'review' => 'warning',
                        'book' => 'primary',
                        'video' => 'danger',
                        'website' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('authors')
                    ->limit(25)
                    ->placeholder('Unknown'),

                Tables\Columns\TextColumn::make('publication')
                    ->limit(20)
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('quality_score')
                    ->label('Quality')
                    ->badge()
                    ->color(fn (?int $state): string => match (true) {
                        $state >= 8 => 'success',
                        $state >= 6 => 'warning',
                        $state >= 4 => 'info',
                        $state >= 1 => 'danger',
                        default => 'gray',
                    })
                    ->placeholder('N/R'),

                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean(),

                Tables\Columns\TextColumn::make('publication_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'study' => 'Research Study',
                        'article' => 'Article',
                        'review' => 'Review/Meta-analysis',
                        'book' => 'Book',
                        'video' => 'Video',
                        'website' => 'Website',
                    ]),
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verified'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_url')
                    ->label('View')
                    ->icon('heroicon-o-link')
                    ->url(fn ($record): ?string => $record->url)
                    ->openUrlInNewTab()
                    ->visible(fn ($record): bool => !empty($record->url)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}