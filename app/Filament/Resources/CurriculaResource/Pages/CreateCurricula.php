<?php

namespace App\Filament\Resources\CurriculaResource\Pages;

use App\Filament\Resources\CurriculaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCurricula extends CreateRecord
{
    protected static string $resource = CurriculaResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
