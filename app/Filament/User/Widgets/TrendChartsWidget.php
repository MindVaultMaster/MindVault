<?php

namespace App\Filament\User\Widgets;

use App\Models\JournalEntry;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class TrendChartsWidget extends ChartWidget
{
    protected static ?string $heading = '🧠 Cognitive Performance Trends (Last 30 Days)';

    protected int | string | array $columnSpan = 'full';

    protected static ?string $description = 'Track your cognitive enhancement journey with visual trend analysis';

    protected function getData(): array
    {
        $userId = Auth::id();

        // Get last 30 days of data
        $entries = JournalEntry::where('user_id', $userId)
            ->whereDate('entry_date', '>=', now()->subDays(30))
            ->whereDate('entry_date', '<=', now())
            ->orderBy('entry_date')
            ->get();

        // Prepare data for the last 30 days
        $labels = [];
        $focusData = [];
        $moodData = [];
        $energyData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('M j');

            $entry = $entries->where('entry_date', $date)->first();

            $focusData[] = $entry?->overall_focus ?? null;
            $moodData[] = $entry?->overall_mood ?? null;
            $energyData[] = $entry?->overall_energy ?? null;
        }

        return [
            'datasets' => [
                [
                    'label' => '🎯 Focus',
                    'data' => $focusData,
                    'borderColor' => '#2563EB',
                    'backgroundColor' => 'rgba(37, 99, 235, 0.1)',
                    'tension' => 0.4,
                    'spanGaps' => true,
                    'borderWidth' => 3,
                    'pointBackgroundColor' => '#2563EB',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                ],
                [
                    'label' => '😊 Mood',
                    'data' => $moodData,
                    'borderColor' => '#059669',
                    'backgroundColor' => 'rgba(5, 150, 105, 0.1)',
                    'tension' => 0.4,
                    'spanGaps' => true,
                    'borderWidth' => 3,
                    'pointBackgroundColor' => '#059669',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                ],
                [
                    'label' => '⚡ Energy',
                    'data' => $energyData,
                    'borderColor' => '#D97706',
                    'backgroundColor' => 'rgba(217, 119, 6, 0.1)',
                    'tension' => 0.4,
                    'spanGaps' => true,
                    'borderWidth' => 3,
                    'pointBackgroundColor' => '#D97706',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'max' => 5,
                    'min' => 1,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Rating (1-5)',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Date',
                    ],
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
            'elements' => [
                'point' => [
                    'radius' => 3,
                    'hoverRadius' => 6,
                ],
            ],
        ];
    }
}
