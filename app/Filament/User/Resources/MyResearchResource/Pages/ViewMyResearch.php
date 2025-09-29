<?php

namespace App\Filament\User\Resources\MyResearchResource\Pages;

use App\Filament\User\Resources\MyResearchResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMyResearch extends ViewRecord
{
    protected static string $resource = MyResearchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}