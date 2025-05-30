<?php

namespace App\Filament\Resources\BookmarkLembagaResource\Pages;

use App\Filament\Resources\BookmarkLembagaResource;
use Filament\Resources\Pages\ListRecords;

class ListBookmarkLembagas extends ListRecords
{
    protected static string $resource = BookmarkLembagaResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Hapus CreateAction agar admin tidak bisa membuat bookmark
    }
}