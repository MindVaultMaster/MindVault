<?php

namespace App\Filament\User\Resources\SubstanceResource\Pages;

use App\Filament\User\Resources\SubstanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewSubstance extends ViewRecord
{
    protected static string $resource = SubstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn (): bool => $this->record->created_by_user_id === Auth::id()),
        ];
    }
}