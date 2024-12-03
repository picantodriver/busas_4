<?php

namespace App\Filament\Resources\AcadYearsResource\Pages;

use App\Filament\Resources\AcadYearsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcadYears extends EditRecord
{
    protected static string $resource = AcadYearsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
