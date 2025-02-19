<?php

namespace App\Filament\Resources\StudentsResource\Pages;

use App\Filament\Resources\StudentsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CreateStudents extends CreateRecord
{
    protected static string $resource = StudentsResource::class;

    protected function validateGradeData(array $grade, bool $isRegular = true): void
    {
        $requiredFields = ['course_code', 'final_grade'];

        // Check if course exists
        $course = DB::table('courses')->where('course_code', $grade['course_code'])->first();

        // For irregular students with non-existing courses, require additional fields
        if (!$isRegular && !$course) {
            $requiredFields[] = 'descriptive_title';
            $requiredFields[] = 'course_unit';
        }

        foreach ($requiredFields as $field) {
            if (!isset($grade[$field]) || empty($grade[$field])) {
                throw new InvalidArgumentException("Missing required grade field: {$field}");
            }
        }
    }

    protected function validateFinalRecord(array $record): void
    {
        $requiredFields = ['curricula_id', 'course_code', 'descriptive_title', 'final_grade', 'course_unit'];
        foreach ($requiredFields as $field) {
            if (!isset($record[$field]) || empty($record[$field])) {
                throw new InvalidArgumentException("Missing required field in final record: {$field}");
            }
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Create the student record
        $student = static::getModel()::create($data);

        Log::info('Created student:', ['id' => $student->id]);

        // Get default academic term
        $acadTerm = DB::table('acad_terms')->orderBy('id')->first();
        if (!$acadTerm) {
            throw new InvalidArgumentException('No academic term found in the database.');
        }
        $acadTermId = $acadTerm->id;

        // Handle regular student records
        if ($data['is_regular']) {
            if (isset($data['records_regular'])) {
                Log::info('Regular records data:', $data['records_regular']);

                foreach ($data['records_regular'] as $record) {
                    if (!isset($record['curricula_id'])) {
                        throw new InvalidArgumentException('Missing curricula_id for regular student record');
                    }

                    if (isset($record['records_regular_grades'])) {
                        foreach ($record['records_regular_grades'] as $grade) {
                            $this->validateGradeData($grade, true);

                            $course = DB::table('courses')->where('course_code', $grade['course_code'])->first();

                            if (!$course) {
                                throw new InvalidArgumentException('Invalid course_code: ' . $grade['course_code']);
                            }

                            $student->records()->create([
                                'curricula_id' => $record['curricula_id'],
                                'course_id' => $course->id,
                                'course_code' => $grade['course_code'],
                                'descriptive_title' => $course->descriptive_title,
                                'final_grade' => $grade['final_grade'],
                                'removal_rating' => $grade['removal_rating'] ?? null,
                                'course_unit' => $course->course_unit,
                                'acad_term_id' => $acadTermId,
                                'is_regular' => true
                            ]);
                        }
                    }
                }
            }
        }
        // Handle irregular student records
        else {
            if (isset($data['campus_college_records'])) {
                Log::info('Irregular records data:', $data['campus_college_records']);

                foreach ($data['campus_college_records'] as $campusRecord) {
                    if (isset($campusRecord['records_irregular'])) {
                        foreach ($campusRecord['records_irregular'] as $record) {
                            if (!isset($record['curricula_id'])) {
                                throw new InvalidArgumentException('Missing curricula_id for irregular student record');
                            }

                            if (isset($record['records_regular_grades'])) {
                                foreach ($record['records_regular_grades'] as $grade) {
                                    // Find the course using the grade's course code
                                    $course = DB::table('courses')->where('course_code', $grade['course_code'])->first();

                                    $studentRecord = [
                                        'curricula_id' => $record['curricula_id'],
                                        'course_code' => $grade['course_code'],
                                        'final_grade' => $grade['final_grade'],
                                        'removal_rating' => $grade['removal_rating'] ?? null,
                                        'is_regular' => false,
                                        'acad_term_id' => $acadTermId,
                                    ];

                                    // If this is a predefined course, use its details
                                    if ($course) {
                                        $studentRecord['course_id'] = $course->id;
                                        $studentRecord['descriptive_title'] = $course->descriptive_title;
                                        $studentRecord['course_unit'] = $course->course_unit;
                                    } else {
                                        // For custom courses, use the values provided in the form
                                        $studentRecord['course_id'] = null; // No matching course
                                        if (!isset($grade['descriptive_title']) || empty($grade['descriptive_title'])) {
                                            throw new InvalidArgumentException("Missing required grade field: descriptive_title");
                                        }
                                        if (!isset($grade['course_unit']) || empty($grade['course_unit'])) {
                                            throw new InvalidArgumentException("Missing required grade field: course_unit");
                                        }
                                        $studentRecord['descriptive_title'] = $grade['descriptive_title'];
                                        $studentRecord['course_unit'] = $grade['course_unit'];
                                    }

                                    // Only validate the final populated record
                                    $this->validateFinalRecord($studentRecord);

                                    $student->records()->create($studentRecord);
                                }
                            }
                        }
                    }
                }
            }
        }

        // Handle ladderized graduation information
        if (isset($data['ladderized']) && is_array($data['ladderized'])) {
            Log::info('Ladderized data received:', $data['ladderized']);

            foreach ($data['ladderized'] as $ladderizedItem) {
                if (empty($ladderizedItem)) continue;

                $ladderizedData = [
                    'student_id' => $student->id,
                    'board_approval' => $ladderizedItem['board_approval'] ?? null,
                    'latin_honor' => $ladderizedItem['latin_honor'] ?? null,
                    'program_cert' => $ladderizedItem['program_cert'] ?? null,
                    'graduation_date' => $ladderizedItem['graduation_date'] ?? null,
                ];

                Log::info('Creating ladderized record with data:', $ladderizedData);

                // Create the record using the relationship
                $student->ladderized()->create($ladderizedData);
            }
        }

        // Handle graduation information
        if (isset($data['graduationInfos'])) {
            Log::info('Graduation info data:', $data['graduationInfos']);

            $graduationData = $data['graduationInfos'];
            if (
                !isset($graduationData['graduation_date']) || !isset($graduationData['board_approval']) ||
                !isset($graduationData['dates_of_attendance'])
            ) {
                throw new InvalidArgumentException('Missing required graduation information fields');
            }

            $student->graduationInfos()->create([
                'graduation_date' => $graduationData['graduation_date'],
                'board_approval' => $graduationData['board_approval'],
                'latin_honor' => $graduationData['latin_honor'] ?? null,
                'degree_attained' => $graduationData['degree_attained'] ?? null,
                'dates_of_attendance' => $graduationData['dates_of_attendance']
            ]);
        }

        // Handle registration information
        if (isset($data['registrationInfos'])) {
            Log::info('Registration info data:', $data['registrationInfos']);

            $registrationData = $data['registrationInfos'];
            $requiredFields = ['last_school_attended', 'last_year_attended', 'category', 'acad_year_id', 'acad_term_id'];

            foreach ($requiredFields as $field) {
                if (!isset($registrationData[$field])) {
                    throw new InvalidArgumentException("Missing required registration field: {$field}");
                }
            }

            $student->registrationInfos()->create([
                'last_school_attended' => $registrationData['last_school_attended'],
                'last_year_attended' => $registrationData['last_year_attended'],
                'category' => $registrationData['category'],
                'other_category' => $registrationData['other_category'] ?? null,
                'acad_year_id' => $registrationData['acad_year_id'],
                'acad_term_id' => $registrationData['acad_term_id']
            ]);
        }

        return $student;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return Action::make('create')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
            ->requiresConfirmation()
            ->action(fn() => $this->create())
            ->keyBindings(['mod+s']);
    }
}
