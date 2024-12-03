<?php

namespace App\Filament\Resources\CurriculaAcadTermsResource\Pages;

use App\Filament\Resources\CurriculaAcadTermsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCurriculaAcadTerms extends ListRecords
{
    protected static string $resource = CurriculaAcadTermsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
