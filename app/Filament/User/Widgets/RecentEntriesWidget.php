<?php

namespace App\Filament\User\Widgets;

use App\Models\JournalEntry;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RecentEntriesWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Journal Entries';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                JournalEntry::query()
                    ->where('user_id', Auth::id())
                    ->latest('entry_date')
                    ->latest('entry_time')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('entry_date')
                    ->label('Date')
                    ->date('M j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->limit(30)
                    ->placeholder('No title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('content')
                    ->label('Preview')
                    ->limit(50)
                    ->placeholder('No content')
                    ->wrap(),

                Tables\Columns\TextColumn::make('overall_focus')
                    ->label('Focus')
                    ->badge()
                    ->color(fn (?int $state): string => match ($state) {
                        1, 2 => 'danger',
                        3 => 'warning',
                        4, 5 => 'success',
                        default => 'gray',
                    })
                    ->placeholder('N/R'),

                Tables\Columns\TextColumn::make('overall_mood')
                    ->label('Mood')
                    ->badge()
                    ->color(fn (?int $state): string => match ($state) {
                        1, 2 => 'danger',
                        3 => 'warning',
                        4, 5 => 'success',
                        default => 'gray',
                    })
                    ->placeholder('N/R'),

                Tables\Columns\TextColumn::make('substances.name')
                    ->label('Substances')
                    ->badge()
                    ->color('info')
                    ->separator(', ')
                    ->limit(2)
                    ->placeholder('None'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->url(fn (JournalEntry $record): string => route('filament.user.resources.journal-entries.view', $record))
                    ->openUrlInNewTab(false),
                Tables\Actions\Action::make('edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn (JournalEntry $record): string => route('filament.user.resources.journal-entries.edit', $record))
                    ->openUrlInNewTab(false),
            ])
            ->emptyStateHeading('No journal entries yet')
            ->emptyStateDescription('Start tracking your nootropics journey by creating your first journal entry.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Create Journal Entry')
                    ->url(route('filament.user.resources.journal-entries.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ]);
    }
}