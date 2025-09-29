<?php

namespace App\Filament\User\Resources\FeedbackResource\Pages;

use App\Filament\User\Resources\FeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFeedback extends ViewRecord
{
    protected static string $resource = FeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn ($record) => $record->status === 'open'),
        ];
    }
}