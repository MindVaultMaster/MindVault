<?php

namespace App\Filament\User\Resources\FeedbackResource\Pages;

use App\Filament\User\Resources\FeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeedback extends EditRecord
{
    protected static string $resource = FeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn ($record) => $record->status === 'open'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}