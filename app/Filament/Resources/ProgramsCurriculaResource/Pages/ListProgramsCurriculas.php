<?php

namespace App\Filament\Resources\ProgramsCurriculaResource\Pages;

use App\Filament\Resources\ProgramsCurriculaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgramsCurriculas extends ListRecords
{
    protected static string $resource = ProgramsCurriculaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
