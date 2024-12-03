<?php

namespace App\Filament\Resources\StudentsGraduationInfosResource\Pages;

use App\Filament\Resources\StudentsGraduationInfosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentsGraduationInfos extends EditRecord
{
    protected static string $resource = StudentsGraduationInfosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
