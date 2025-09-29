<?php

namespace App\Filament\Resources\NootropicRequestResource\Pages;

use App\Filament\Resources\NootropicRequestResource;
use App\Models\Substance;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewNootropicRequest extends ViewRecord
{
    protected static string $resource = NootropicRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

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

            Actions\Action::make('reject')
                ->label('Reject Request')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => !$this->record->is_predefined)
                ->requiresConfirmation()
                ->modalHeading('Reject Nootropic Request')
                ->modalDescription('This will delete this user-submitted nootropic request. This action cannot be undone.')
                ->action(function () {
                    $this->record->delete();

                    $this->redirect(static::getResource()::getUrl('index'));
                })
                ->successNotificationTitle('Nootropic request rejected and removed'),
        ];
    }
}