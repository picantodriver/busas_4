<?php

namespace App\Filament\Resources\CampusesResource\Pages;

use App\Filament\Resources\CampusesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCampuses extends ListRecords
{
    protected static string $resource = CampusesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->icon('heroicon-o-plus')
            ->label('New Campuses'),
        ];
    }
}
