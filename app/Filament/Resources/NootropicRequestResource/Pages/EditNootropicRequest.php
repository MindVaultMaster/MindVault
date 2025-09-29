<?php

namespace App\Filament\Resources\NootropicRequestResource\Pages;

use App\Filament\Resources\NootropicRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNootropicRequest extends EditRecord
{
    protected static string $resource = NootropicRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),

            Actions\Action::make('approve')
                ->label('Approve Request')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => !$this->record->is_predefined)
                ->requiresConfirmation()
                ->modalHeading('Approve Nootropic Request')
                ->modalDescription('This will approve this user-submitted nootropic and make it available as an official substance for all users.')
                ->action(function () {
                    $this->record->update([
                        'is_predefined' => true,
                        'is_public' => true,
                    ]);

                    $this->redirect(static::getResource()::getUrl('index'));
                })
                ->successNotificationTitle('Nootropic request approved successfully'),

            Actions\DeleteAction::make()
                ->label('Reject Request'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Medication request updated successfully!';
    }
}