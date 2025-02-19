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

        $record->load([
            'graduationInfos',
            'registrationInfos',
            'records.curricula',
            'ladderized'
        ]);

        return $record;
    }

    public function mutateFormDataBeforeFill(array $data): array
    {
        $student = $this->getRecord();

        $data['full_name'] = $student->first_name . ' ' . $student->last_name;

        // Add ladderized data if it exists
        if ($student->ladderized()->exists()) {
            $data['ladderized'] = $student->ladderized->map(function ($item) {
                return [
                    'board_approval' => $item->board_approval,
                    'program_cert' => $item->program_cert,
                    'graduation_date' => $item->graduation_date,
                    'latin_honor' => $item->latin_honor ?? null,
                ];
            })->toArray();
        }

        if ($student->records()->exists()) {
            $data['is_regular'] = $student->records->first()->is_regular ?? true;

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
        // Group records by curricula_id
        $groupedRecords = $student->records->groupBy('curricula_id');

        $formattedRecords = [];

        foreach ($groupedRecords as $curriculaId => $records) {
            $curricula = Curricula::find($curriculaId);

            $grades = [];
            foreach ($records as $record) {
                $grades[] = [
                    'course_code' => $record->course_code ?? '',
                    'descriptive_title' => $record->descriptive_title ?? '',
                    'final_grade' => $record->final_grade ?? '',
                    'removal_rating' => $record->removal_rating ?? '',
                    'course_unit' => $this->formatCourseUnit($record->course_unit),
                ];
            }

            $formattedRecords[] = [
                'curricula_id' => $curriculaId,
                'records_regular_grades' => $grades,
            ];
        }

        return $formattedRecords;
    }

    protected function formatIrregularRecords(Students $student): array
    {
        $formattedRecords = [];

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
        return (string) $courseUnit;
    }

    public function mutateFormDataBeforeSave(array $data): array
    {
        // Handle ladderized data
        if (isset($data['ladderized'])) {
            foreach ($data['ladderized'] as &$ladderized) {
                // Ensure graduation_date is in the correct format
                if (isset($ladderized['graduation_date'])) {
                    $ladderized['graduation_date'] = date('Y-m-d', strtotime($ladderized['graduation_date']));
                }

                // Add student_id to each ladderized record
                $ladderized['student_id'] = $this->getRecord()->id;
            }
        }

        if (isset($data['records_regular'])) {
            foreach ($data['records_regular'] as &$record) {
                if (isset($record['records_regular_grades'])) {
                    foreach ($record['records_regular_grades'] as &$grade) {
                        if (isset($grade['course_unit'])) {
                            $grade['course_unit'] = (string) $grade['course_unit'];
                        }
                    }
                }
            }
        }

        if (isset($data['records_irregular'])) {
            foreach ($data['records_irregular'] as &$record) {
                if (isset($record['course_unit'])) {
                    $record['course_unit'] = (string) $record['course_unit'];
                }
            }
        }

        if (!isset($data['status']) || empty($data['status'])) {
            $data['status'] = 'unverified';
        }

        return $data;
    }

    public function afterSave(): void
    {
        // Update or create ladderized records
        if (isset($this->data['ladderized'])) {
            $student = $this->getRecord();

            $student->ladderized()->delete();

            foreach ($this->data['ladderized'] as $ladderizedData) {
                $student->ladderized()->create($ladderizedData);
            }
        }

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
