<?php

namespace App\Filament\User\Resources\NootropicRequestResource\Pages;

use App\Filament\User\Resources\NootropicRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNootropicRequests extends ListRecords
{
    protected static string $resource = NootropicRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Share New Nootropic'),
        ];
    }
}