<?php

namespace App\Filament\Resources\AcadTermsResource\Pages;

use App\Filament\Resources\AcadTermsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcadTerms extends EditRecord
{
    protected static string $resource = AcadTermsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
