<?php

namespace App\Filament\User\Resources\JournalEntryResource\Pages;

use App\Filament\User\Resources\JournalEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateJournalEntry extends CreateRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        // Store substance tags to attach after creation
        $this->substanceTags = $data['substance_tags'] ?? [];
        unset($data['substance_tags']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Attach substances to the journal entry
        if (!empty($this->substanceTags)) {
            foreach ($this->substanceTags as $substanceId) {
                $this->record->substances()->attach($substanceId, [
                    'taken_at' => $this->record->entry_date . ' ' . ($this->record->entry_time ?? '08:00:00'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    protected array $substanceTags = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}