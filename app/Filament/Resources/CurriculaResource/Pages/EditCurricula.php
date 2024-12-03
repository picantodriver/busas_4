<?php

namespace App\Filament\Resources\CurriculaResource\Pages;

use App\Filament\Resources\CurriculaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCurricula extends EditRecord
{
    protected static string $resource = CurriculaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
