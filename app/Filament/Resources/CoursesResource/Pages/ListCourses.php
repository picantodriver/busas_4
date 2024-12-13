<?php

namespace App\Filament\Resources\CoursesResource\Pages;

use App\Filament\Resources\CoursesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourses extends ListRecords
{
    protected static string $resource = CoursesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->using(function (array $data, string $model) {
                #dd($value);
                    foreach ($data['courses'] as $key => $value) {
                        // dd($value['course_code']);
                        $model::create([
                            'curricula_id' => $data['curricula_id'],
                            'course_code' => $value['course_code'],
                            'descriptive_title' => $value['descriptive_title'],
                            'course_unit' => $value['course_unit'],
                            'created_by' => \Illuminate\Support\Facades\Auth::user()->id
                        ]);
                    }
                    return;
                }),
        ];
    }
}
