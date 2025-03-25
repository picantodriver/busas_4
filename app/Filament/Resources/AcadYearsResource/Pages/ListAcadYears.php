<?php

namespace App\Filament\Resources\AcadYearsResource\Pages;

use App\Filament\Resources\AcadYearsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcadYears extends ListRecords
{
    protected static string $resource = AcadYearsResource::class;
    protected static ?string $title = "Academic Years";
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('New Academic Years & Terms')
            ->icon('heroicon-o-plus')
            ->modalHeading('New Academic Year & Term'),
        ];
    }
}
