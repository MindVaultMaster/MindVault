<?php

namespace App\Filament\User\Resources\ResearchLibraryResource\Pages;

use App\Filament\User\Resources\ResearchLibraryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateResearchLibrary extends CreateRecord
{
    protected static string $resource = ResearchLibraryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['added_by_user_id'] = Auth::id();
        $data['is_verified'] = false; // User submissions start unverified
        $data['is_public'] = false; // User research is private by default

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
