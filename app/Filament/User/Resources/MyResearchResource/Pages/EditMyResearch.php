<?php

namespace App\Filament\User\Resources\MyResearchResource\Pages;

use App\Filament\User\Resources\MyResearchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyResearch extends EditRecord
{
    protected static string $resource = MyResearchResource::class;

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