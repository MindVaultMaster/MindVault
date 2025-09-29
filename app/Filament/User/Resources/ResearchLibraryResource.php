<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\ResearchLibraryResource\Pages;
use App\Models\SubstanceResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ResearchLibraryResource extends Resource
{
    protected static ?string $model = SubstanceResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Research Library';

    protected static ?string $navigationGroup = '🔬 Community';

    public static function canCreate(): bool
    {
        return true; // Users can create research entries
    }

    public static function canEdit($record): bool
    {
        return $record->added_by_user_id === Auth::id(); // Users can only edit their own research entries
    }

    public static function canDelete($record): bool
    {
        return $record->added_by_user_id === Auth::id(); // Users can only delete their own research entries
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('substance_id')
                    ->label('Substance')
                    ->relationship('substance', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Title of the research paper, article, or study'),

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

                Forms\Components\TextInput::make('authors')
                    ->placeholder('Author names')
                    ->maxLength(255),

                Forms\Components\TextInput::make('publication')
                    ->placeholder('Journal, magazine, or publication name')
                    ->maxLength(255),

                Forms\Components\DatePicker::make('publication_date')
                    ->label('Publication Date'),

                Forms\Components\TextInput::make('url')
                    ->label('URL')
                    ->url()
                    ->placeholder('Link to the full research paper or article'),

                Forms\Components\TextInput::make('doi')
                    ->label('DOI')
                    ->placeholder('Digital Object Identifier (if available)'),

                Forms\Components\Textarea::make('description')
                    ->placeholder('Brief description of the research')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('abstract')
                    ->placeholder('Research abstract or summary')
                    ->rows(4)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('key_findings')
                    ->label('Key Findings')
                    ->placeholder('Main findings and conclusions')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\TagsInput::make('tags')
                    ->placeholder('Add relevant tags'),

                Forms\Components\Select::make('quality_score')
                    ->label('Quality Score (1-10)')
                    ->options(array_combine(range(1, 10), range(1, 10)))
                    ->placeholder('Rate the quality of this research'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(SubstanceResource::query()->where(function ($query) {
                $query->where('is_verified', true) // Show all verified admin research
                      ->orWhere('added_by_user_id', Auth::id()); // Show user's own research
            }))
            ->columns([
                Tables\Columns\TextColumn::make('substance.name')
                    ->label('Substance')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->title),

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
                        default => 'gray',
                    })
                    ->placeholder('N/R'),

                Tables\Columns\TextColumn::make('publication_date')
                    ->date()
                    ->sortable()
                    ->placeholder('Unknown'),

                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean()
                    ->tooltip('Admin verified research'),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Shared')
                    ->boolean()
                    ->tooltip('Shared with community'),

                Tables\Columns\TextColumn::make('addedBy.name')
                    ->label('Added By')
                    ->placeholder('Admin')
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                    ->visible(fn (SubstanceResource $record): bool =>
                        $record->added_by_user_id === Auth::id()
                    ),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (SubstanceResource $record): bool =>
                        $record->added_by_user_id === Auth::id()
                    ),

                Tables\Actions\Action::make('view_details')
                    ->label('Details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn ($record) => $record->title)
                    ->modalContent(fn ($record) => view('filament.modals.research-details', ['record' => $record]))
                    ->modalWidth('4xl'),

                Tables\Actions\Action::make('view_url')
                    ->label('View Source')
                    ->icon('heroicon-o-link')
                    ->url(fn (SubstanceResource $record): ?string => $record->url)
                    ->openUrlInNewTab()
                    ->visible(fn (SubstanceResource $record): bool => !empty($record->url)),
            ])
            ->defaultSort('publication_date', 'desc')
            ->emptyStateHeading('No Research Available')
            ->emptyStateDescription('Check back later as we continue to add research papers and studies.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResearchLibraries::route('/'),
            'create' => Pages\CreateResearchLibrary::route('/create'),
            'view' => Pages\ViewResearchLibrary::route('/{record}'),
            'edit' => Pages\EditResearchLibrary::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_verified', true)->count();
    }
}
