<?php

namespace App\Filament\User\Resources\SubstanceResource\Pages;

use App\Filament\User\Resources\SubstanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditSubstance extends EditRecord
{
    protected static string $resource = SubstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn (): bool =>
                    $this->record->created_by_user_id === Auth::id() &&
                    !$this->record->is_predefined
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}