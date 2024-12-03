<?php

namespace App\Filament\Resources\StudentsRecordsResource\Pages;

use App\Filament\Resources\StudentsRecordsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentsRecords extends ListRecords
{
    protected static string $resource = StudentsRecordsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
