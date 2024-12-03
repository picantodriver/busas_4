<?php

namespace App\Filament\Resources\StudentsRegistrationInfosResource\Pages;

use App\Filament\Resources\StudentsRegistrationInfosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentsRegistrationInfos extends EditRecord
{
    protected static string $resource = StudentsRegistrationInfosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
