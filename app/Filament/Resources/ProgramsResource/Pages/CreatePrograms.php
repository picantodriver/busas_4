<?php

namespace App\Filament\Resources\ProgramsResource\Pages;

use App\Filament\Resources\ProgramsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePrograms extends CreateRecord
{
    // protected function beforeCreate(): void
    // {
    //     $data = $this->form->getState();

    //     if (empty($data['program_major_name'])) {
    //         Notification::make()
    //             ->title('Action Required')
    //             ->body('Please enter a Program Major Name before creating a new program. If no major is needed, delete the existing one first.')
    //             ->danger() 
    //             ->send();

    //         $this->halt(); // Prevents the form from submitting
    //     }
    // }
    protected static string $resource = ProgramsResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
