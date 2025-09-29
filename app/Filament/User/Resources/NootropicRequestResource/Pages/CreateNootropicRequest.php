<?php

namespace App\Filament\User\Resources\NootropicRequestResource\Pages;

use App\Filament\User\Resources\NootropicRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNootropicRequest extends CreateRecord
{
    protected static string $resource = NootropicRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Nootropic shared with community successfully!';
    }
}