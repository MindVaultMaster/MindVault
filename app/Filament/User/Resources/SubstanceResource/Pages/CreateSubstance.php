<?php

namespace App\Filament\User\Resources\SubstanceResource\Pages;

use App\Filament\User\Resources\SubstanceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSubstance extends CreateRecord
{
    protected static string $resource = SubstanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_user_id'] = Auth::id();
        $data['is_predefined'] = false;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}