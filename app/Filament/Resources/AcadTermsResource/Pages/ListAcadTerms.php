<?php

namespace App\Filament\Resources\AcadTermsResource\Pages;

use App\Filament\Resources\AcadTermsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcadTerms extends ListRecords
{
    protected static string $resource = AcadTermsResource::class;

    protected static ?string $title = "Academic Terms";
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
