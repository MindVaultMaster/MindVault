<?php

namespace App\Filament\User\Resources\NootropicRequestResource\Pages;

use App\Filament\User\Resources\NootropicRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNootropicRequest extends EditRecord
{
    protected static string $resource = NootropicRequestResource::class;

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

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Nootropic request updated successfully!';
    }
}