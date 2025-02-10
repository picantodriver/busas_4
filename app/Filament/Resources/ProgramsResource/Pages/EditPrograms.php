<?php

namespace App\Filament\Resources\ProgramsResource\Pages;

use App\Filament\Resources\ProgramsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Colleges;

class EditPrograms extends EditRecord
{
    protected static string $resource = ProgramsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->icon('heroicon-o-trash')
            ->label('Delete Record'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['campus_id'] = Colleges::where('id', $data['college_id'])->first()->campus_id;

        return $data;
    }
}
