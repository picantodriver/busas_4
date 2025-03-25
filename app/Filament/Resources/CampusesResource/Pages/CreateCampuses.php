<?php

namespace App\Filament\Resources\CampusesResource\Pages;

use App\Filament\Resources\CampusesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCampuses extends CreateRecord
{
    protected static string $resource = CampusesResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
