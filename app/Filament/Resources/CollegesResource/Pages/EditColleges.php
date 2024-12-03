<?php

namespace App\Filament\Resources\CollegesResource\Pages;

use App\Filament\Resources\CollegesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditColleges extends EditRecord
{
    protected static string $resource = CollegesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
