<?php

namespace App\Filament\Resources\NootropicRequestResource\Pages;

use App\Filament\Resources\NootropicRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListNootropicRequests extends ListRecords
{
    protected static string $resource = NootropicRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action since admins don't create requests
        ];
    }
}