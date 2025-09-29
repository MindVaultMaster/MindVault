<?php

namespace App\Filament\User\Resources\JournalEntryResource\Pages;

use App\Filament\User\Resources\JournalEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJournalEntry extends EditRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load existing substance tags
        $data['substance_tags'] = $this->record->substances()->pluck('substances.id')->toArray();
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store substance tags to sync after save
        $this->substanceTags = $data['substance_tags'] ?? [];
        unset($data['substance_tags']);
        return $data;
    }

    protected function afterSave(): void
    {
        // Sync substances with the journal entry
        $syncData = [];
        foreach ($this->substanceTags as $substanceId) {
            $syncData[$substanceId] = [
                'taken_at' => $this->record->entry_date . ' ' . ($this->record->entry_time ?? '08:00:00'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        $this->record->substances()->sync($syncData);
    }

    protected array $substanceTags = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}