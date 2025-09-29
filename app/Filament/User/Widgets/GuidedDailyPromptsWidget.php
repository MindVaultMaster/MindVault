<?php

namespace App\Filament\User\Widgets;

use App\Models\JournalEntry;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class GuidedDailyPromptsWidget extends Widget
{
    protected static string $view = 'filament.user.widgets.guided-daily-prompts';
    protected int | string | array $columnSpan = 'full';

    public $showModal = false;
    public $quickContent = '';
    public $quickFocus = null;
    public $quickMood = null;
    public $quickEnergy = null;

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->quickContent = '';
        $this->quickFocus = null;
        $this->quickMood = null;
        $this->quickEnergy = null;
    }

    public function saveQuickLog()
    {
        if (empty($this->quickContent)) {
            Notification::make()
                ->title('Content required')
                ->danger()
                ->send();
            return;
        }

        JournalEntry::create([
            'user_id' => Auth::id(),
            'title' => 'Quick Log - ' . now()->format('M j, Y'),
            'content' => $this->quickContent,
            'entry_date' => now()->toDateString(),
            'entry_time' => now()->format('H:i'),
            'overall_focus' => $this->quickFocus,
            'overall_mood' => $this->quickMood,
            'overall_energy' => $this->quickEnergy,
            'is_public' => false,
        ]);

        $this->closeModal();

        Notification::make()
            ->title('Quick log saved!')
            ->success()
            ->send();

        $this->redirect(request()->header('Referer'));
    }

    protected function hasEntryToday(): bool
    {
        return JournalEntry::where('user_id', Auth::id())
            ->whereDate('entry_date', now()->toDateString())
            ->exists();
    }
}
