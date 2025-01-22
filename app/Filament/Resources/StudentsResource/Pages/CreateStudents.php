<?php

namespace App\Filament\Resources\StudentsResource\Pages;

use App\Filament\Resources\StudentsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStudents extends CreateRecord
{
    protected static string $resource = StudentsResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Create the student record
        $student = static::getModel()::create($data);

        // Create student records
        foreach ($data['records'] as $record) {
            foreach ($record['courses'] as $course) {
                $student->records()->create([
                    'student_id' => $student->id,
                    'final_grades' => $course['final_grades'],
                    'removal_rating' => $course['removal_rating'],
                    'acad_term_id' => $record['acad_term_id'],
                    'course_id' => $course['course_id'],
                    'descriptive_title' => $course['descriptive_title'],
                    'units_of_credit' => $course['units_of_credit'],
                ]);
                dd($data);
            }
        }

        // Create graduation information
        $student->graduationInfos()->create([
            'student_id' => $student->id,
            'graduation_date' => $data['graduation_date'],
            'board_approval' => $data['board_approval'],
            'latin_honor' => $data['latin_honor'],
            'degree_attained' => $data['degree_attained'],
            'dates_of_attendance' => $data['dates_of_attendance'],
        ]);

        // Create registration information
        $student->registrationInfos()->create([
            'student_id' => $student->id,
            'last_school_attended' => $data['last_school_attended'],
            'last_year_attended' => $data['last_year_attended'],
            'category' => $data['category'],
            'acad_year_id' => $data['acad_year_id'],
            'acad_term_id' => $data['acad_term_id'],
        ]);

        return $student;
    }
}
