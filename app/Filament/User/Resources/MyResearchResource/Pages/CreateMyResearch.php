<?php

namespace App\Filament\User\Resources\MyResearchResource\Pages;

use App\Filament\User\Resources\MyResearchResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMyResearch extends CreateRecord
{
    protected static string $resource = MyResearchResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['added_by_user_id'] = Auth::id();
        $data['is_verified'] = false; // User research starts unverified
        $data['is_public'] = false; // User research is private by default

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}