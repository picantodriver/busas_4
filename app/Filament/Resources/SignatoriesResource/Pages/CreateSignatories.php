<?php

namespace App\Filament\Resources\SignatoriesResource\Pages;

use App\Filament\Resources\SignatoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSignatories extends CreateRecord
{
    protected static string $resource = SignatoriesResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
