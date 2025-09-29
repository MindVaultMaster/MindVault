<?php

namespace App\Filament\User\Widgets;

use App\Models\JournalEntry;
use App\Models\Substance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();

        // Total journal entries
        $totalEntries = JournalEntry::where('user_id', $userId)->count();

        // Entries this month
        $thisMonthEntries = JournalEntry::where('user_id', $userId)
            ->whereMonth('entry_date', now()->month)
            ->whereYear('entry_date', now()->year)
            ->count();

        // Average focus rating this month
        $avgFocus = JournalEntry::where('user_id', $userId)
            ->whereMonth('entry_date', now()->month)
            ->whereYear('entry_date', now()->year)
            ->whereNotNull('overall_focus')
            ->avg('overall_focus');

        // Most used substance this month
        $mostUsedSubstance = DB::table('substance_entries')
            ->join('journal_entries', 'substance_entries.journal_entry_id', '=', 'journal_entries.id')
            ->join('substances', 'substance_entries.substance_id', '=', 'substances.id')
            ->where('journal_entries.user_id', $userId)
            ->whereMonth('journal_entries.entry_date', now()->month)
            ->whereYear('journal_entries.entry_date', now()->year)
            ->select('substances.name', DB::raw('count(*) as usage_count'))
            ->groupBy('substances.name')
            ->orderBy('usage_count', 'desc')
            ->first();

        // Tracked substances count
        $trackedSubstances = DB::table('substance_entries')
            ->join('journal_entries', 'substance_entries.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entries.user_id', $userId)
            ->distinct('substance_entries.substance_id')
            ->count();

        return [
            Stat::make('🧠 Total Entries', $totalEntries)
                ->description('Journal entries recorded')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart([7, 12, 9, 15, 18, 22, $totalEntries > 0 ? min(30, $totalEntries) : 5])
                ->extraAttributes(['class' => 'brain-pulse']),

            Stat::make('📅 This Month', $thisMonthEntries)
                ->description('Entries this month')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success')
                ->chart([1, 3, 5, 7, 10, 12, $thisMonthEntries])
                ->extraAttributes(['class' => 'cognitive-boost']),

            Stat::make('🎯 Avg Focus', $avgFocus ? number_format($avgFocus, 1) . '/5' : 'No data')
                ->description('Average focus this month')
                ->descriptionIcon('heroicon-m-eye')
                ->color($avgFocus ? ($avgFocus >= 4 ? 'success' : ($avgFocus >= 3 ? 'warning' : 'danger')) : 'gray')
                ->chart($avgFocus ? [2.5, 3.0, 3.2, 3.8, 4.1, 4.0, round($avgFocus, 1)] : [0])
                ->extraAttributes(['class' => 'focus-enhancement']),

            Stat::make('⚗️ Substances', $trackedSubstances)
                ->description('Different substances tried')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('info')
                ->chart([1, 2, 3, 4, 5, 6, $trackedSubstances])
                ->extraAttributes(['class' => 'neural-network']),

            Stat::make('⭐ Most Used', $mostUsedSubstance ? $mostUsedSubstance->name : 'None')
                ->description($mostUsedSubstance ? "Used {$mostUsedSubstance->usage_count} times this month" : 'No substances tracked this month')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning')
                ->chart($mostUsedSubstance ? [1, 2, 3, 4, 5, 6, $mostUsedSubstance->usage_count] : [0])
                ->extraAttributes(['class' => 'synapse-fire']),
        ];
    }
}
