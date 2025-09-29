<?php

namespace App\Filament\User\Resources\ResearchLibraryResource\Pages;

use App\Filament\User\Resources\ResearchLibraryResource;
use Filament\Resources\Pages\ListRecords;

class ListResearchLibraries extends ListRecords
{
    protected static string $resource = ResearchLibraryResource::class;

    public function getTitle(): string
    {
        return 'Research Library';
    }

    public function getHeading(): string
    {
        return 'Research Library';
    }

    public function getSubheading(): ?string
    {
        return 'Scientific studies and articles about nootropics and cognitive enhancement';
    }
}