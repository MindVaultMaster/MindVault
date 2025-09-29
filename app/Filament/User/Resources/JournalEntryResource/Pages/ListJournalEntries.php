<?php

namespace App\Filament\User\Resources\JournalEntryResource\Pages;

use App\Filament\User\Resources\JournalEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJournalEntries extends ListRecords
{
    protected static string $resource = JournalEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Journal Entry'),
        ];
    }
}