<?php

namespace App\Filament\Resources\CurriculaResource\Pages;

use App\Filament\Resources\CurriculaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCurriculas extends ListRecords
{
    protected static string $resource = CurriculaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('New Curricula')
            ->icon('heroicon-o-plus'),
        ];
    }
}
