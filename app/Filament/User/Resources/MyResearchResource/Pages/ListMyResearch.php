<?php

namespace App\Filament\User\Resources\MyResearchResource\Pages;

use App\Filament\User\Resources\MyResearchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyResearch extends ListRecords
{
    protected static string $resource = MyResearchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'My Research';
    }

    public function getHeading(): string
    {
        return 'My Research';
    }

    public function getSubheading(): ?string
    {
        return 'Your personal research collection and notes';
    }
}