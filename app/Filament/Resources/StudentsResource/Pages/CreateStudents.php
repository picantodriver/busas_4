<?php

namespace App\Filament\Resources\StudentsResource\Pages;

use App\Filament\Resources\StudentsResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Filament\Actions\Action;
use Exception;

class CreateStudents extends CreateRecord
{
    protected static string $resource = StudentsResource::class;

    protected function getDocumentContent($document): ?string
{
    try {
        if ($document instanceof \Illuminate\Http\UploadedFile) {
            $content = $document->get();
            Log::info('Got content from UploadedFile', ['size' => strlen($content)]);
            return $content;
        }

        if (is_string($document)) {
            $paths = [
                storage_path('app/public/' . $document),
                storage_path('app/documents/' . $document)
            ];

            foreach ($paths as $path) {
                if (file_exists($path)) {
                    $content = file_get_contents($path);
                    Log::info('Successfully read file content', ['path' => $path, 'size' => strlen($content)]);
                    return $content;
                }
            }
        }

        if (is_array($document) && !empty($document)) {
            $firstFile = $document[0] ?? $document;
            if ($firstFile instanceof \Illuminate\Http\UploadedFile) {
                return $firstFile->get();
            }
            if (is_string($firstFile)) {
                return $this->getDocumentContent($firstFile);
            }
        }

        Log::warning('Unable to process document', ['type' => gettype($document)]);
        return null;
    } catch (\Exception $e) {
        Log::error('Error processing document', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return null;
    }
}
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
        // Start a database transaction to ensure atomicity
        return DB::transaction(function () use ($data) {
            try {
                Log::info('Received data:', $data);

                // Validate and create student record
                $student = static::getModel()::create($data);
                Log::info('Created student:', ['id' => $student->id]);

                // Get default academic term
                $acadTerm = DB::table('acad_terms')->orderBy('id')->first();
                if (!$acadTerm) {
                    throw new InvalidArgumentException('No academic term found in the database.');
                }
                $acadTermId = $acadTerm->id;

                // Validate and set campus, program, and college IDs
                list($campusId, $programId, $collegeId, $programMajorId) = $this->validateAndGetStudentIds($data);

                // Handle student records based on regular/irregular status
                $this->handleStudentRecords($student, $data, $acadTermId, $campusId, $programId, $collegeId, $programMajorId);

                // Handle ladderized graduation information
                $this->handleLadderizedGraduation($student, $data);

                // Handle graduation information
                $this->handleGraduationInfo($student, $data);

                // Handle registration information
                $this->handleRegistrationInfo($student, $data);

                return $student;
            } catch (Exception $e) {
                // Log the full error details
                Log::error('Error in student record creation', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'data' => $data
                ]);

                // Explicitly rollback the transaction
                DB::rollBack();

                // Throw the exception to be handled by Filament
                throw $e;
            }
        });
    }

    /**
     * Validate and retrieve student IDs
     * 
     * @param array $data
     * @return array
     * @throws InvalidArgumentException
     */
    protected function validateAndGetStudentIds(array $data): array
    {
        $campusId = null;
        $programId = null;
        $collegeId = null;
        $programMajorId = null;

        if ($data['is_regular']) {
            // Validate required IDs for regular students
            $requiredIds = ['campus_id', 'program_id', 'college_id'];
            foreach ($requiredIds as $requiredId) {
                if (!isset($data[$requiredId]) || empty($data[$requiredId])) {
                    throw new InvalidArgumentException("{$requiredId} is required for student records");
                }
            }
            
            // Set the values for regular students
            $campusId = $data['campus_id'];
            $programId = $data['program_id'];
            $collegeId = $data['college_id'];
            $programMajorId = $data['program_major_id'] ?? null;
        } else {
            // For irregular students, check if campus_college_records exists and has at least one entry
            if (!isset($data['campus_college_records']) || empty($data['campus_college_records'])) {
                throw new InvalidArgumentException("campus_college_records is required for irregular students");
            }

            // Get IDs from the first campus_college_record
            $campusRecord = $data['campus_college_records'][0];
            $requiredIds = ['campus_id', 'program_id', 'college_id'];
            foreach ($requiredIds as $requiredId) {
                if (!isset($campusRecord[$requiredId]) || empty($campusRecord[$requiredId])) {
                    throw new InvalidArgumentException("{$requiredId} is required for student records");
                }
            }

            // Set the values for irregular students
            $campusId = $campusRecord['campus_id'];
            $programId = $campusRecord['program_id'];
            $collegeId = $campusRecord['college_id'];
            $programMajorId = $campusRecord['program_major_id'] ?? null;
        }

        return [$campusId, $programId, $collegeId, $programMajorId];
    }

    /**
     * Handle student records for regular and irregular students
     * 
     * @param Model $student
     * @param array $data
     * @param int $acadTermId
     * @param int $campusId
     * @param int $programId
     * @param int $collegeId
     * @param int|null $programMajorId
     * @throws InvalidArgumentException
     */
    protected function handleStudentRecords(
        Model $student, 
        array $data, 
        int $acadTermId, 
        int $campusId, 
        int $programId, 
        int $collegeId, 
        ?int $programMajorId
    ) {
        // Logic for handling regular and irregular student records 
        // (Same as in the original method, but extracted for better readability)
        if ($data['is_regular']) {
            $this->handleRegularStudentRecords($student, $data, $acadTermId, $campusId, $programId, $collegeId, $programMajorId);
        } else {
            $this->handleIrregularStudentRecords($student, $data, $acadTermId, $campusId, $programId, $collegeId, $programMajorId);
        }
    }

    /**
     * Handle ladderized graduation information
     * 
     * @param Model $student
     * @param array $data
     * @throws InvalidArgumentException
     */
    protected function handleLadderizedGraduation(Model $student, array $data)
    {
        if (isset($data['ladderized']) && is_array($data['ladderized'])) {
            Log::info('Ladderized data received:', $data['ladderized']);

            foreach ($data['ladderized'] as $ladderizedItem) {
                if (empty($ladderizedItem)) continue;

                $acadYear = DB::table('acad_years')->orderBy('id')->first();
                if (!$acadYear) {
                    throw new InvalidArgumentException('No academic year found in the database.');
                }
                $acadYearId = $acadYear->id;

                $ladderizedData = [
                    'student_id' => $student->id,
                    'board_approval' => $ladderizedItem['board_approval'] ?? null,
                    'latin_honor' => $ladderizedItem['latin_honor'] ?? null,
                    'program_cert' => $ladderizedItem['program_cert'] ?? null,
                    'graduation_date' => $ladderizedItem['graduation_date'] ?? null,
                    'acad_year_id' => $ladderizedItem['acad_year_id'] ?? $acadYearId,
                ];

                Log::info('Creating ladderized record with data:', $ladderizedData);
                $student->ladderized()->create($ladderizedData);
            }
        }
    }

    /**
     * Handle graduation information
     * 
     * @param Model $student
     * @param array $data
     * @throws InvalidArgumentException
     */
    protected function handleGraduationInfo(Model $student, array $data)
    {
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
    }

    /**
     * Handle registration information
     * 
     * @param Model $student
     * @param array $data
     * @throws InvalidArgumentException
     */
    protected function handleRegistrationInfo(Model $student, array $data)
    {
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
    }

    /**
     *Handle regular student records
     * 
     * @param Model $student
     * @param array $data
     * @param int $acadTermId
     * @param int $campusId
     * @param int $programId
     * @param int $collegeId
     * @param int|null $programMajorId
     * @throws InvalidArgumentException
     */
    protected function handleRegularStudentRecords(
        Model $student, 
        array $data, 
        int $acadTermId, 
        int $campusId, 
        int $programId, 
        int $collegeId, 
        ?int $programMajorId
    ) {
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
                            'is_regular' => true,
                            'campus_id' => $campusId,
                            'program_id' => $programId,
                            'college_id' => $collegeId,
                            'program_major_id' => $programMajorId
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Handle irregular student records
     * 
     * @param Model $student
     * @param array $data
     * @param int $acadTermId
     * @param int $campusId
     * @param int $programId
     * @param int $collegeId
     * @param int|null $programMajorId
     * @throws InvalidArgumentException
     */
    protected function handleIrregularStudentRecords(
        Model $student, 
        array $data, 
        int $acadTermId, 
        int $campusId, 
        int $programId, 
        int $collegeId, 
        ?int $programMajorId
    ) {
        // Process document for irregular students
        $fileContent = isset($data['document']) ? $this->getDocumentContent($data['document']) : null;

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
                                $course = DB::table('courses')->where('course_code', $grade['course_code'])->first();

                                $studentRecord = [
                                    'curricula_id' => $record['curricula_id'],
                                    'course_code' => $grade['course_code'],
                                    'final_grade' => $grade['final_grade'],
                                    'removal_rating' => $grade['removal_rating'] ?? null,
                                    'is_regular' => false,
                                    'acad_term_id' => $acadTermId,
                                    'campus_id' => $campusId,
                                    'program_id' => $programId,
                                    'college_id' => $collegeId,
                                    'program_major_id' => $programMajorId
                                ];

                                if ($course) {
                                    $studentRecord['course_id'] = $course->id;
                                    $studentRecord['descriptive_title'] = $course->descriptive_title;
                                    $studentRecord['course_unit'] = $course->course_unit;
                                } else {
                                    $studentRecord['course_id'] = null;
                                    if (!isset($grade['descriptive_title']) || empty($grade['descriptive_title'])) {
                                        throw new InvalidArgumentException("Missing required grade field: descriptive_title");
                                    }
                                    if (!isset($grade['course_unit']) || empty($grade['course_unit'])) {
                                        throw new InvalidArgumentException("Missing required grade field: course_unit");
                                    }
                                    $studentRecord['descriptive_title'] = $grade['descriptive_title'];
                                    $studentRecord['course_unit'] = $grade['course_unit'];
                                }

                                $this->validateFinalRecord($studentRecord);

                                // Create record with attachment for irregular students
                                $record = $student->records()->create($studentRecord);

                                if ($fileContent !== null) {
                                    try {
                                        $record->update(['attachment' => $fileContent]);
                                        Log::info('Successfully updated record with attachment', ['record_id' => $record->id]);
                                    } catch (\Exception $e) {
                                        Log::error('Failed to update record with attachment', [
                                            'error' => $e->getMessage(),
                                            'record_id' => $record->id
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
            ->requiresConfirmation()
            ->action(fn() => $this->create())
            ->keyBindings(['mod+s']);
    }
}