<?php

namespace App\Filament\User\Resources\FeedbackResource\Pages;

use App\Filament\User\Resources\FeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeedback extends ListRecords
{
    protected static string $resource = FeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Submit Feedback')
                ->icon('heroicon-o-plus'),
        ];
    }
}