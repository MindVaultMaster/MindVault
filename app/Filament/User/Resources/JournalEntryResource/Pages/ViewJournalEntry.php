<?php

namespace App\Filament\User\Resources\JournalEntryResource\Pages;

use App\Filament\User\Resources\JournalEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJournalEntry extends ViewRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}