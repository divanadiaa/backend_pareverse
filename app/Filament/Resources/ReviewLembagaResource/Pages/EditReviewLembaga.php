<?php

namespace App\Filament\Resources\ReviewLembagaResource\Pages;

use App\Filament\Resources\ReviewLembagaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReviewLembaga extends EditRecord
{
    protected static string $resource = ReviewLembagaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
