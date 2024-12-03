<?php

namespace App\Filament\Resources\ProgramsMajorResource\Pages;

use App\Filament\Resources\ProgramsMajorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgramsMajors extends ListRecords
{
    protected static string $resource = ProgramsMajorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
