<?php

namespace App\Filament\Resources\StudentsRecordsResource\Pages;

use App\Filament\Resources\StudentsRecordsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStudentsRecords extends CreateRecord
{
    protected static string $resource = StudentsRecordsResource::class;

}
