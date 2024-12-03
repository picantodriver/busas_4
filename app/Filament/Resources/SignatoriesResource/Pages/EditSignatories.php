<?php

namespace App\Filament\Resources\SignatoriesResource\Pages;

use App\Filament\Resources\SignatoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSignatories extends EditRecord
{
    protected static string $resource = SignatoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
