<?php

namespace App\Filament\Resources\ProgramsCurriculaResource\Pages;

use App\Filament\Resources\ProgramsCurriculaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgramsCurricula extends EditRecord
{
    protected static string $resource = ProgramsCurriculaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
