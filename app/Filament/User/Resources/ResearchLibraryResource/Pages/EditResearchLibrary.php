<?php

namespace App\Filament\User\Resources\ResearchLibraryResource\Pages;

use App\Filament\User\Resources\ResearchLibraryResource;
use Filament\Resources\Pages\EditRecord;

class EditResearchLibrary extends EditRecord
{
    protected static string $resource = ResearchLibraryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}