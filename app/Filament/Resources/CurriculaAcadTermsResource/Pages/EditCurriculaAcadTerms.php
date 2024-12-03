<?php

namespace App\Filament\Resources\CurriculaAcadTermsResource\Pages;

use App\Filament\Resources\CurriculaAcadTermsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCurriculaAcadTerms extends EditRecord
{
    protected static string $resource = CurriculaAcadTermsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
