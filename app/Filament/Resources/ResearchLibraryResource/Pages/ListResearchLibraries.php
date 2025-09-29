<?php

namespace App\Filament\Resources\ResearchLibraryResource\Pages;

use App\Filament\Resources\ResearchLibraryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResearchLibraries extends ListRecords
{
    protected static string $resource = ResearchLibraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
