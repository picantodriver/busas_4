<?php

namespace App\Filament\Resources\SignatoriesResource\Pages;

use App\Filament\Resources\SignatoriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSignatories extends ListRecords
{
    protected static string $resource = SignatoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->icon('heroicon-o-plus')
            ->label('New Signatories'),
        ];
    }
}
