<?php

namespace App\Filament\User\Resources\NootropicRequestResource\Pages;

use App\Filament\User\Resources\NootropicRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewNootropicRequest extends ViewRecord
{
    protected static string $resource = NootropicRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}