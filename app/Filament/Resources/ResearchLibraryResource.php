<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResearchLibraryResource\Pages;
use App\Filament\Resources\ResearchLibraryResource\RelationManagers;
use App\Models\SubstanceResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResearchLibraryResource extends Resource
{
    protected static ?string $model = SubstanceResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Research Library';

    protected static ?string $navigationGroup = 'Nootropics Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('substance_id')
                    ->relationship('substance', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),

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
                    ->maxLength(2048)
                    ->columnSpanFull(),

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
                        1 => '1 - Very Poor',
                        2 => '2 - Poor',
                        3 => '3 - Below Average',
                        4 => '4 - Fair',
                        5 => '5 - Average',
                        6 => '6 - Above Average',
                        7 => '7 - Good',
                        8 => '8 - Very Good',
                        9 => '9 - Excellent',
                        10 => '10 - Outstanding',
                    ]),

                Forms\Components\Textarea::make('abstract')
                    ->rows(4)
                    ->columnSpanFull(),

                Forms\Components\TagsInput::make('tags')
                    ->placeholder('Add tags like: memory, focus, safety, dosage')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('key_findings')
                    ->label('Key Findings/Summary')
                    ->rows(4)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_verified')
                    ->label('Verified')
                    ->helperText('Mark as verified by administrators'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('substance.name')
                    ->label('Substance')
                    ->sortable()
                    ->searchable(),

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
                    ->limit(30)
                    ->placeholder('Unknown'),

                Tables\Columns\TextColumn::make('publication')
                    ->limit(25)
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
                    ->sortable()
                    ->placeholder('Unknown'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('substance')
                    ->relationship('substance', 'name'),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'study' => 'Research Study',
                        'article' => 'Article',
                        'review' => 'Review/Meta-analysis',
                        'book' => 'Book',
                        'video' => 'Video',
                        'website' => 'Website',
                    ]),

                Tables\Filters\Filter::make('quality_score')
                    ->form([
                        Forms\Components\Select::make('min_quality')
                            ->label('Minimum Quality Score')
                            ->options([
                                6 => '6+ (Above Average)',
                                7 => '7+ (Good)',
                                8 => '8+ (Very Good)',
                                9 => '9+ (Excellent)',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_quality'],
                                fn (Builder $query, $score): Builder => $query->where('quality_score', '>=', $score),
                            );
                    }),

                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verified'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_url')
                    ->label('View')
                    ->icon('heroicon-o-link')
                    ->url(fn (SubstanceResource $record): ?string => $record->url)
                    ->openUrlInNewTab()
                    ->visible(fn (SubstanceResource $record): bool => !empty($record->url)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResearchLibraries::route('/'),
            'create' => Pages\CreateResearchLibrary::route('/create'),
            'edit' => Pages\EditResearchLibrary::route('/{record}/edit'),
        ];
    }
}
