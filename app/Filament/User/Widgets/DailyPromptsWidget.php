<?php

namespace App\Filament\User\Widgets;

use App\Models\JournalEntry;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class DailyPromptsWidget extends Widget
{
    protected static string $view = 'filament.user.widgets.daily-prompts';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $userId = Auth::id();
        $today = now()->toDateString();

        // Check if user has already logged today
        $hasEntryToday = JournalEntry::where('user_id', $userId)
            ->whereDate('entry_date', $today)
            ->exists();

        // Get random daily prompts
        $prompts = $this->getDailyPrompts();

        // Get today's specific prompt based on day of year to be consistent
        $dayOfYear = now()->dayOfYear;
        $promptIndex = $dayOfYear % count($prompts);
        $todaysPrompt = $prompts[$promptIndex];

        // Get recent trends
        $recentEntries = JournalEntry::where('user_id', $userId)
            ->orderBy('entry_date', 'desc')
            ->limit(7)
            ->get();

        $avgFocus = $recentEntries->whereNotNull('overall_focus')->avg('overall_focus');
        $avgMood = $recentEntries->whereNotNull('overall_mood')->avg('overall_mood');
        $avgSleep = $recentEntries->whereNotNull('overall_sleep')->avg('overall_sleep');

        return [
            'hasEntryToday' => $hasEntryToday,
            'todaysPrompt' => $todaysPrompt,
            'avgFocus' => $avgFocus,
            'avgMood' => $avgMood,
            'avgSleep' => $avgSleep,
            'recentEntries' => $recentEntries,
        ];
    }

    private function getDailyPrompts(): array
    {
        return [
            "How did your substances affect your productivity today?",
            "What side effects, if any, did you notice from your stack?",
            "How was your sleep quality after taking your evening supplements?",
            "Did you notice any mood changes throughout the day?",
            "What was your energy level like compared to baseline?",
            "How did your focus and concentration feel during work/study?",
            "Did you experience any anxiety or jitters from stimulants?",
            "What was your appetite like today?",
            "How did your substances affect your social interactions?",
            "Did you notice any cognitive improvements or impairments?",
            "How was your motivation level throughout the day?",
            "What physical sensations did you notice from your stack?",
            "Did your substances help with any specific tasks or goals?",
            "How did timing affect the effectiveness of your substances?",
            "What would you change about today's dosing or timing?",
            "Did you stack any substances together? How did that feel?",
            "How was your stress management with today's supplements?",
            "Did you notice any effects on your creativity or problem-solving?",
            "How did your substances affect your exercise performance?",
            "What insights did you gain about your optimal dosing today?",
        ];
    }
}
