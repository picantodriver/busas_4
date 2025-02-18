<?php

namespace App\Filament\Resources\StudentsResource\Pages;

use App\Filament\Resources\StudentsResource;
use App\Models\Students;
use App\Models\Curricula;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class EditStudents extends EditRecord
{
    protected static string $resource = StudentsResource::class;

    public function getRecord(): Model
    {
        $record = parent::getRecord();
<<<<<<< Updated upstream
        
=======

>>>>>>> Stashed changes
        $record->load([
            'graduationInfos',
            'registrationInfos',
            'records.curricula'
        ]);

        return $record;
    }

    public function mutateFormDataBeforeFill(array $data): array
    {
        $student = $this->getRecord();

        $data['full_name'] = $student->first_name . ' ' . $student->last_name;
<<<<<<< Updated upstream
        
        if ($student->records()->exists()) {
            $data['is_regular'] = $student->records->first()->is_regular ?? true;
            
=======

        if ($student->records()->exists()) {
            $data['is_regular'] = $student->records->first()->is_regular ?? true;

>>>>>>> Stashed changes
            if ($data['is_regular']) {
                $data['records_regular'] = $this->formatRegularRecords($student);
            } else {
                $data['records_irregular'] = $this->formatIrregularRecords($student);
            }
        }

        return $data;
    }

    protected function formatRegularRecords(Students $student): array
    {
        $formattedRecords = [];
<<<<<<< Updated upstream
        
        foreach ($student->records as $record) {
            // Clean and format course unit
            $courseUnit = $this->formatCourseUnit($record->course_unit);
    
=======

        foreach ($student->records as $record) {
            // Clean and format course unit
            $courseUnit = $this->formatCourseUnit($record->course_unit);

>>>>>>> Stashed changes
            $formattedRecords[] = [
                'curricula_id' => $record->curricula->curricula_name ?? null,
                'records_regular_grades' => [
                    [
                        'course_code' => $record->course_code ?? '',
                        'descriptive_title' => $record->descriptive_title ?? '',
                        'final_grade' => $record->final_grade ?? '',
                        'removal_rating' => $record->removal_rating ?? '',
                        'course_unit' => $courseUnit,
                    ]
                ],
            ];
        }
<<<<<<< Updated upstream
    
        return $formattedRecords;
    }
    
    protected function formatIrregularRecords(Students $student): array
    {
        $formattedRecords = [];
        
=======

        return $formattedRecords;
    }

    protected function formatIrregularRecords(Students $student): array
    {
        $formattedRecords = [];

>>>>>>> Stashed changes
        foreach ($student->records as $record) {
            // Clean and format course unit
            $courseUnit = $this->formatCourseUnit($record->course_unit);

            $formattedRecords[] = [
                'curricula_name' => $record->curricula_name ?? '',
                'course_code' => $record->course_code ?? '',
                'descriptive_title' => $record->descriptive_title ?? '',
                'final_grade' => $record->final_grade ?? '',
                'removal_rating' => $record->removal_rating ?? '',
                'course_unit' => $courseUnit,
            ];
        }

        return $formattedRecords;
    }

    protected function formatCourseUnit($courseUnit): string
    {
        // Remove any existing parentheses and trim whitespace
        return (string) $courseUnit;
    }

    public function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['records_regular'])) {
            foreach ($data['records_regular'] as &$record) {
                if (isset($record['records_regular_grades'])) {
                    foreach ($record['records_regular_grades'] as &$grade) {
                        if (isset($grade['course_unit'])) {
<<<<<<< Updated upstream
                            $grade['course_unit'] = (string) $grade['course_unit']; 
=======
                            $grade['course_unit'] = (string) $grade['course_unit'];
>>>>>>> Stashed changes
                        }
                    }
                }
            }
        }

        if (isset($data['records_irregular'])) {
            foreach ($data['records_irregular'] as &$record) {
                if (isset($record['course_unit'])) {
<<<<<<< Updated upstream
                    $record['course_unit'] = (string) $record['course_unit'];
=======
                    $record['course_unit'] = (string) $record['course_unit']; // ✅ Keep it as-is
>>>>>>> Stashed changes
                }
            }
        }

<<<<<<< Updated upstream
        if (!isset($data['status']) || empty($data['status'])) {
            $data['status'] = 'unverified';
        }

=======
>>>>>>> Stashed changes
        return $data;
    }

    public function afterSave(): void
    {
        $this->redirect(StudentsResource::getUrl('index'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->label('Delete Record'),
        ];
    }
}
