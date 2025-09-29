<?php

namespace App\Filament\User\Resources\FeedbackResource\Pages;

use App\Filament\User\Resources\FeedbackResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateFeedback extends CreateRecord
{
    protected static string $resource = FeedbackResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['status'] = 'open';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Feedback submitted successfully! We appreciate your input.';
    }
}