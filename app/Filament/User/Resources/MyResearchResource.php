<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyResearchResource\Pages;
use App\Models\SubstanceResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyResearchResource extends Resource
{
    protected static ?string $model = SubstanceResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'My Research';

    protected static ?string $navigationGroup = '🔬 Community';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('added_by_user_id', Auth::id());
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
                    ->placeholder('Title of your research paper, article, or study'),

                Forms\Components\Select::make('type')
                    ->options([
                        'study' => 'Research Study',
                        'article' => 'Article',
                        'review' => 'Review/Meta-analysis',
                        'book' => 'Book',
                        'video' => 'Video',
                        'website' => 'Website',
                        'personal_notes' => 'Personal Research Notes',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('authors')
                    ->placeholder('Author names (or your name for personal research)')
                    ->maxLength(255),

                Forms\Components\TextInput::make('publication')
                    ->placeholder('Journal, magazine, or publication name')
                    ->maxLength(255),

                Forms\Components\DatePicker::make('publication_date')
                    ->label('Publication Date'),

                Forms\Components\TextInput::make('url')
                    ->label('URL')
                    ->url()
                    ->placeholder('Link to the research or your notes'),

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
            ->columns([
                Tables\Columns\TextColumn::make('substance.name')
                    ->label('Substance')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40)
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
                        'personal_notes' => 'purple',
                        default => 'gray',
                    }),

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

                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->tooltip(fn ($record) => $record->is_verified ? 'Verified' : 'Personal/Unverified'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
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
                        'personal_notes' => 'Personal Research Notes',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('view_url')
                    ->label('View Source')
                    ->icon('heroicon-o-link')
                    ->url(fn (SubstanceResource $record): ?string => $record->url)
                    ->openUrlInNewTab()
                    ->visible(fn (SubstanceResource $record): bool => !empty($record->url)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No Research Added Yet')
            ->emptyStateDescription('Start building your personal research collection by adding studies, articles, or your own research notes.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyResearch::route('/'),
            'create' => Pages\CreateMyResearch::route('/create'),
            'view' => Pages\ViewMyResearch::route('/{record}'),
            'edit' => Pages\EditMyResearch::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('added_by_user_id', Auth::id())->count();
    }
}
