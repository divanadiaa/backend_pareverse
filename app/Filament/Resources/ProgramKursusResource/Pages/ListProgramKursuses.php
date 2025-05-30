<?php

namespace App\Filament\Resources\ProgramKursusResource\Pages;

use App\Filament\Resources\ProgramKursusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgramKursuses extends ListRecords
{
    protected static string $resource = ProgramKursusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
