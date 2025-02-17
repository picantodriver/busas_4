<?php
namespace App\Filament\Resources\StudentsResource\Pages;

use App\Filament\Resources\StudentsResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Filament\Actions\Action;

class CreateStudents extends CreateRecord
{
    protected static string $resource = StudentsResource::class;

    protected static bool $canCreateAnother = false;

    protected function validateGradeData(array $grade): void 
    {
        $requiredFields = ['course_code', 'final_grade'];
        
        foreach ($requiredFields as $field) {
            if (!isset($grade[$field]) || empty($grade[$field])) {
                throw new InvalidArgumentException("Missing required grade field: {$field}");
            }
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        Log::info('Received data:', $data);
        // Create the student record
        $student = static::getModel()::create($data);
        
        Log::info('Created student:', ['id' => $student->id]);

<<<<<<< Updated upstream
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
=======
        // Handle regular student records
        if (isset($data['records_regular']) && $data['is_regular']) {
            Log::info('Regular records data:', $data['records_regular']);

            foreach ($data['records_regular'] as $record) {
                if (!isset($record['curricula_id'])) {
                    throw new InvalidArgumentException('Missing curricula_id for regular student record');
                }

                if (isset($record['records_regular_grades'])) {
                    foreach ($record['records_regular_grades'] as $grade) {
                        $this->validateGradeData($grade);

                        $course = DB::table('courses')->where('course_code', $grade['course_code'])->first();
                        $acadTerm = DB::table('acad_terms')->orderBy('id')->first();

                        if (!$acadTerm) {
                            throw new InvalidArgumentException('No academic term found in the database.');
                        }
                        
                        $acadTermId = $acadTerm->id;

                        if (!$course) {
                            throw new InvalidArgumentException('Invalid course_code: ' . $grade['course_code']);
                        }
        
                        
                        $student->records()->create([
                            'curricula_id' => $record['curricula_id'],
                            'course_id' => $course->id,
                            'course_code' => $grade['course_code'],
                            'descriptive_title' => $course->descriptive_title,
                            'final_grade' => $grade['final_grade'],
                            'removal_rating' => $grade['removal_rating'] ?? null, // This can be null
                            'course_unit' => $course->course_unit,
                            'acad_term_id' => $acadTermId, 
                        ]);
                    }
                }
>>>>>>> Stashed changes
            }
        }

        // Handle irregular student records
        if (isset($data['records_irregular']) && !$data['is_regular']) {
            foreach ($data['records_irregular'] as $record) {
                if (!isset($record['curricula_name'])) {
                    throw new InvalidArgumentException('Missing curricula_name for irregular student record');
                }

                if (isset($record['records_irregular_grades'])) {
                    foreach ($record['records_irregular_grades'] as $grade) {
                        $this->validateGradeData($grade);
                        
                        $student->records()->create([
                            'curricula_id' => $record['curricula_id'],
                            'course_id' => $course->id,
                            'course_code' => $grade['course_code'],
                            'descriptive_title' => $grade['descriptive_title'],
                            'final_grade' => $grade['final_grade'],
                            'removal_rating' => $grade['removal_rating'] ?? null, // This can be null
                            'course_unit' => $grade['course_unit'],
                            'acad_term_id' => $acadTermId, 
                        ]);
                    }
                }
            }
        }

        // Create graduation information if the data exists
        if (isset($data['graduationInfos'])) {
            Log::info('Graduation info data:', $data['graduationInfos']);
            
            $graduationData = $data['graduationInfos'];
            if (!isset($graduationData['graduation_date']) || !isset($graduationData['board_approval']) || 
                !isset($graduationData['dates_of_attendance'])) {
                throw new InvalidArgumentException('Missing required graduation information fields');
            }

            $student->graduationInfos()->create([
                'graduation_date' => $graduationData['graduation_date'],
                'board_approval' => $graduationData['board_approval'],
                'latin_honor' => $graduationData['latin_honor'] ?? null, // These can be null
                'degree_attained' => $graduationData['degree_attained'] ?? null,
                'dates_of_attendance' => $graduationData['dates_of_attendance']
            ]);
        }

        // Create registration information if the data exists
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
                'other_category' => $registrationData['other_category'] ?? null, // This can be null
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
        ->action(fn () => $this->create())
        ->keyBindings(['mod+s']);
}
}
