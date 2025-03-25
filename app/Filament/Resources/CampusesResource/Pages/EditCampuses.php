<?php

namespace App\Filament\Resources\CampusesResource\Pages;

use App\Filament\Resources\CampusesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCampuses extends EditRecord
{
    protected static string $resource = CampusesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
