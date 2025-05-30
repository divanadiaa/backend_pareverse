<?php

namespace App\Filament\Resources\BookmarkLembagaResource\Pages;

use App\Filament\Resources\BookmarkLembagaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookmarkLembaga extends EditRecord
{
    protected static string $resource = BookmarkLembagaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
