<?php

namespace App\Filament\Resources\StudentsResource\Pages;

use App\Filament\Resources\StudentsResource;
use App\Models\Students;
use App\Models\Curricula;
use App\Models\Campuses;
use App\Models\Colleges;
use App\Models\Programs;
use App\Models\ProgramsMajor;
use App\Models\StudentsRecords;
use App\Models\Courses;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\Card;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\TableRepeater;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Receipt;

class ViewStudents extends ViewRecord
{
    protected static string $resource = StudentsResource::class;

    protected function getViewData(): array
    {
        $student = $this->getRecord();

        // This data will be passed to the view
        return [
            'student' => $student,
            'recordsData' => $student->is_regular ?
                $this->formatRegularRecords($student) :
                $this->formatIrregularRecords($student),
            'isRegular' => $student->is_regular
        ];
    }

    protected function getFormSchema(): array
    {
        // Get the student record
        $student = $this->getRecord();

        // Base form schema
        $schema = [
            Section::make('Student Information')
                ->schema([
                    TextInput::make('full_name')
                        ->label('Full Name')
                        ->disabled(),

                    Toggle::make('is_regular')
                        ->label('Regular Student')
                        ->disabled(),

                    TextInput::make('nstp_number')
                        ->label('NSTP Number')
                        ->disabled(),
                ]),

            Section::make('Personal Information')
                ->schema([
                    TextInput::make('personal_info.sex')
                        ->label('Sex')
                        ->disabled(),

                    TextInput::make('personal_info.address')
                        ->label('Address')
                        ->disabled(),

                    TextInput::make('personal_info.birthdate')
                        ->label('Birth Date')
                        ->disabled(),

                    TextInput::make('personal_info.birthplace')
                        ->label('Birth Place')
                        ->disabled(),

                    TextInput::make('personal_info.region')
                        ->label('Region')
                        ->disabled(),

                    TextInput::make('personal_info.province')
                        ->label('Province')
                        ->disabled(),

                    TextInput::make('personal_info.city_municipality')
                        ->label('City/Municipality')
                        ->disabled(),
                ]),

            Section::make('Academic Information')
                ->schema([
                    TextInput::make('campus_id')
                        ->label('Campus')
                        ->disabled(),

                    TextInput::make('college_id')
                        ->label('College')
                        ->disabled(),

                    TextInput::make('program_id')
                        ->label('Program')
                        ->disabled(),

                    TextInput::make('program_major_id')
                        ->label('Major')
                        ->disabled(),

                    TextInput::make('current_semester')
                        ->label('Current Semester')
                        ->disabled(),

                    TextInput::make('current_school_year')
                        ->label('Current School Year')
                        ->disabled(),

                    TextInput::make('gwa')
                        ->label('GWA')
                        ->disabled(),
                ]),
        ];

        // Add regular or irregular section based on student type
        if ($student->is_regular) {
            $schema[] = Section::make('Regular Student Records')
                ->schema([
                    ViewField::make('academic_records_view')
                        ->label('')
                        ->view('filament.resources.students-resource.academic-records-view'),
                ]);
        } else {
            $schema[] = Section::make('Student Records (Irregular)')
                ->visible(true)
                ->schema([
                    // Show document files if any
                    FileUpload::make('document')
                        ->label('Documents')
                        ->disabled()
                        ->columnSpanFull()
                        ->visible(fn() => $student->document),

                    // Campus/College Records Repeater
                    Repeater::make('campus_college_records')
                        ->label('Campus/College Records')
                        ->schema([
                            Grid::make(2)->schema([
                                Select::make('campus_id')
                                    ->label('Campus')
                                    ->options(Campuses::all()->pluck('campus_name', 'id'))
                                    ->disabled()
                                    ->afterStateHydrated(function ($state, $record, $set) {
                                        if ($state) {
                                            $campus = Campuses::find($state);
                                            $set('campus_name', $campus ? $campus->campus_name : 'Unknown Campus');
                                        }
                                    }),

                                Select::make('college_id')
                                    ->label('College')
                                    ->options(Colleges::all()->pluck('college_name', 'id'))
                                    ->disabled()
                                    ->afterStateHydrated(function ($state, $record, $set) {
                                        if ($state) {
                                            $college = Colleges::find($state);
                                            $set('college_name', $college ? $college->college_name : 'Unknown College');
                                        }
                                    }),
                            ]),

                            Grid::make(2)->schema([
                                Select::make('program_id')
                                    ->label('Program')
                                    ->options(Programs::all()->pluck('program_name', 'id'))
                                    ->disabled()
                                    ->afterStateHydrated(function ($state, $record, $set) {
                                        if ($state) {
                                            $program = Programs::find($state);
                                            $set('program_name', $program ? $program->program_name : 'Unknown Program');
                                        }
                                    }),

                                Select::make('program_major_id')
                                    ->label('Program Major')
                                    ->options(ProgramsMajor::all()->pluck('program_major_name', 'id'))
                                    ->disabled()
                                    ->afterStateHydrated(function ($state, $record, $set) {
                                        if ($state) {
                                            $major = ProgramsMajor::find($state);
                                            $set('major_name', $major ? $major->program_major_name : 'Unknown Major');
                                        }
                                    }),
                            ]),

                            // Curriculum Records
                            Repeater::make('records_irregular')
                                ->label('Curriculum')
                                ->schema([
                                    Select::make('curricula_id')
                                        ->label('Curriculum')
                                        ->options(Curricula::all()->pluck('curricula_name', 'id'))
                                        ->disabled()
                                        ->afterStateHydrated(function ($state, $record, $set) {
                                            if ($state) {
                                                $curricula = Curricula::find($state);
                                                $set('curricula_name', $curricula ? $curricula->curricula_name : 'Unknown Curriculum');
                                            }
                                        }),

                                    // Course grades
                                    Repeater::make('records_regular_grades')
                                        ->label('Courses & Grades')
                                        ->schema([
                                            TextInput::make('course_code')
                                                ->label('Course Code')
                                                ->disabled(),

                                            TextInput::make('descriptive_title')
                                                ->label('Descriptive Title')
                                                ->disabled(),

                                            TextInput::make('final_grade')
                                                ->label('Final Grade')
                                                ->disabled(),

                                            TextInput::make('removal_rating')
                                                ->label('Removal Rating')
                                                ->disabled(),

                                            TextInput::make('course_unit')
                                                ->label('Units of Credit')
                                                ->disabled(),
                                        ])
                                        ->disableItemCreation()
                                        ->disableItemDeletion()
                                        ->disableItemMovement()
                                        ->columns(5),
                                ])
                                ->disableItemCreation()
                                ->disableItemDeletion()
                                ->disableItemMovement(),
                        ])
                        ->disableItemCreation()
                        ->disableItemDeletion()
                        ->disableItemMovement(),
                ]);
        }
        return $schema;
    }

    public function getRecord(): Model
    {
        $record = parent::getRecord();

        $record->load([
            'graduationInfos',
            'registrationInfos',
            'records.curricula',
            'ladderized',
            // 'media',
            'records',
        ]);

        return $record;
    }

    public function mutateFormDataBeforeFill(array $data): array
    {
        $student = $this->getRecord();

        $data['full_name'] = ($student->first_name ?? '') . ' ' . ($student->last_name ?? '');

        if ($student->records()->exists()) {
            $latestRecord = $student->records()->latest()->first();

            $data['campus_id'] = $latestRecord->campus_id ?? null;
            $data['college_id'] = $latestRecord->college_id ?? null;
            $data['program_id'] = $latestRecord->program_id ?? null;
            $data['program_major_id'] = $latestRecord->program_major_id ?? null;

            $data['current_semester'] = $latestRecord->semester ?? '';
            $data['current_school_year'] = $latestRecord->school_year ?? '';

            $data['gwa'] = $student->gwa ?? null;
        }

        if ($student->ladderized()->exists()) {
            $data['ladderized'] = $student->ladderized->map(fn($item) => [
                'acad_year_id' => $item->acad_year_id,
                'board_approval' => $item->board_approval,
                'program_cert' => $item->program_cert,
                'graduation_date' => $item->graduation_date,
                'latin_honor' => $item->latin_honor ?? null,
            ])->toArray();
        }

        $data['nstp_number'] = $student->nstp_number ?? null;
        $data['is_regular'] = (bool) $student->is_regular;

        // Handle document data if needed
        if ($student->document) {
            $data['document'] = $student->document;
        }

        // Format structured data for irregular students
        if (!$student->is_regular) {
            Log::info('Processing Irregular Records for student ID: ' . $student->id);

            // Group records by campus, college, program
            $recordsByLocation = [];

            foreach ($student->records as $record) {
                $locationKey = $record->campus_id . '-' . $record->college_id . '-' . $record->program_id;

                if (!isset($recordsByLocation[$locationKey])) {
                    $recordsByLocation[$locationKey] = [
                        'campus_id' => $record->campus_id,
                        'college_id' => $record->college_id,
                        'program_id' => $record->program_id,
                        'program_major_id' => $record->program_major_id,
                        'records_by_curricula' => []
                    ];
                }

                // Group by curricula_id within each location
                $curriculaId = $record->curricula_id;
                if (!isset($recordsByLocation[$locationKey]['records_by_curricula'][$curriculaId])) {
                    $recordsByLocation[$locationKey]['records_by_curricula'][$curriculaId] = [
                        'curricula_id' => $curriculaId,
                        'records_regular_grades' => []
                    ];
                }

                // Add the record to the appropriate group
                $recordsByLocation[$locationKey]['records_by_curricula'][$curriculaId]['records_regular_grades'][] = [
                    'course_code' => $record->course_code,
                    'descriptive_title' => $record->descriptive_title,
                    'final_grade' => $record->final_grade,
                    'removal_rating' => $record->removal_rating,
                    'course_unit' => $this->formatCourseUnit($record->course_unit),
                ];
            }

            // Format for Filament repeaters
            $data['campus_college_records'] = array_map(function ($locationData) {
                $curricula = array_values($locationData['records_by_curricula']);
                return array_merge(
                    [
                        'campus_id' => $locationData['campus_id'],
                        'college_id' => $locationData['college_id'],
                        'program_id' => $locationData['program_id'],
                        'program_major_id' => $locationData['program_major_id'],
                        'records_irregular' => $curricula
                    ]
                );
            }, array_values($recordsByLocation));

            Log::info('Processed campus_college_records: ', [count($data['campus_college_records'])]);
        } else {
            Log::info('Using Regular Records for student ID: ' . $student->id);
            $data['records_regular'] = $this->formatRegularRecords($student);
        }

        $data['personal_info'] = [
            'sex' => $student->sex ?? '',
            'address' => $student->address ?? '',
            'birthdate' => $student->birthdate ?? '',
            'birthplace' => $student->birthplace ?? '',
            'region' => $student->region ?? '',
            'province' => $student->province ?? '',
            'city_municipality' => $student->city_municipality ?? '',
        ];

        return $data;
    }

    protected function formatRegularRecords(Students $student): array
    {
        return $this->formatRecords($student, 'records_regular_grades');
    }

    protected function formatIrregularRecords(Students $student): array
    {
        return $this->formatRecords($student, 'records_irregular_grades');
    }

    private function formatRecords(Students $student, string $key): array
    {
        $groupedRecords = $student->records->groupBy('curricula_id');

        $formattedRecords = [];

        foreach ($groupedRecords as $curriculaId => $records) {
            $curricula = Curricula::find($curriculaId);
            $curriculaName = $curricula ? $curricula->curricula_name : 'Unknown Curriculum';

            $grades = [];
            foreach ($records as $record) {
                $grades[] = [
                    'course_code' => $record->course_code ?? '',
                    'descriptive_title' => $record->descriptive_title ?? '',
                    'final_grade' => $record->final_grade ?? '',
                    'removal_rating' => $record->removal_rating ?? '',
                    'course_unit' => $this->formatCourseUnit($record->course_unit),
                    'semester' => $record->semester ?? '',
                    'school_year' => $record->school_year ?? '',
                ];
            }

            $formattedRecords[] = [
                'curricula_id' => $curriculaId,
                'curricula_name' => $curriculaName,
                $key => $grades,
            ];
        }

        return $formattedRecords;
    }

    protected function formatCourseUnit($courseUnit): string
    {
        return (string) $courseUnit;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to List')
                ->icon('heroicon-o-arrow-left')
                ->url(fn() => StudentsResource::getUrl('index')),

            Actions\Action::make('edit')
                ->label('Edit Student')
                ->icon('heroicon-o-pencil')
                ->url(fn(Students $record) => StudentsResource::getUrl('edit', ['record' => $record])),
            Actions\ActionGroup::make([
                Actions\Action::make('print')
                    ->visible(fn(Students $record) => $record->status !== 'unverified')
                    ->label('Official Transcript of Records')
                    ->icon('heroicon-o-document-text')
                    // ->modalHeading('Select Signatories')
                    ->modalSubmitActionLabel('Generate OTR')
                    ->form([
                        Section::make('Select Signatories')
                            ->schema([
                                Select::make('prepared_by')
                                    ->label('Prepared by')
                                    ->required()
                                    ->searchable()
                                    ->options(function () {
                                        return \App\Models\Signatories::all()->mapWithKeys(function ($signatory) {
                                            return [$signatory->id => $signatory->employee_name . ($signatory->suffix ? ', ' . $signatory->suffix : '')];
                                        });
                                    }),
                                    // Select::make('reviewed_by')
                                    // ->label('Reviewed by')
                                    // ->required()
                                    // ->searchable()
                                    // ->options(function () {
                                    //     return \App\Models\Signatories::where('status', 1)
                                    //         ->get()
                                    //         ->mapWithKeys(function ($signatory) {
                                    //             return [$signatory->id => $signatory->employee_name . ($signatory->suffix ? ', ' . $signatory->suffix : '')];
                                    //         });
                                    // }),
                                    Select::make('reviewed_by')
                                        ->label('Reviewed by')
                                        ->required()
                                        ->searchable()
                                        ->options(function () {
                                            return \App\Models\Signatories::where('is_permanent', '1')
                                                ->get()
                                                ->mapWithKeys(function ($signatory) {
                                                return [$signatory->id => $signatory->employee_name . ($signatory->suffix ? ', ' . $signatory->suffix : '')];
                                            });
                                    }),
                                    Select::make('certified_by')
                                        ->label('Certified Correct by')
                                        ->required()
                                        ->searchable()
                                        ->options(function () {
                                            return \App\Models\Signatories::where('employee_designation', 'University Registrar')
                                                ->get()
                                                ->mapWithKeys(function ($signatory) {
                                                    return [$signatory->id => $signatory->employee_name . ($signatory->suffix ? ', ' . $signatory->suffix : '')];
                                                });
                                        }),
                                ]),
                        Section::make('Enter Receipt Details')
                            ->schema([
                                TextInput::make('or_number')
                                    ->label('Official Receipt Number')
                                    ->required(),
                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->required(),
                                DatePicker::make('date_of_or')
                                    ->label('Date Issued')
                                    ->required(),
                            ])
                    ])
                    ->action(function (array $data, Students $record) {
                        // Get signatory details
                        $preparedBy = \App\Models\Signatories::find($data['prepared_by']);
                        $reviewedBy = \App\Models\Signatories::find($data['reviewed_by']);
                        $certifiedBy = \App\Models\Signatories::find($data['certified_by']);

                        Receipt::create([
                            'or_number' => $data['or_number'],
                            'document_requested' => 'OTR',
                            'amount' => $data['amount'],
                            'date_of_or' => $data['date_of_or'],
                            'user_id' => \Illuminate\Support\Facades\Auth::id(),
                            'date' => now(),
                            'student_id' => $record['id'],
                            
                        ]);

                        Notification::make()
                            ->title('Generating OTR...')
                            ->info()
                            ->send();

                        $record->load([
                            'graduationInfos',
                            'records.curricula',
                            'records.curricula.programMajor',
                        ]);

                        // Generate PDF with proper data and signatories
                        $pdf = PDF::loadView('TOR', [
                            'student' => $record,
                            'preparedBy' => $preparedBy,
                            'reviewedBy' => $reviewedBy,
                            'certifiedBy' => $certifiedBy,
                        ]);

                        // Set PDF options
                        $pdf->setPaper([0, 0, 612, 936], 'portrait');
                        $options = new \Dompdf\Options();
                        $options->set('defaultFont', 'Arial');

                        // Return as downloadable stream
                        return response()->streamDownload(
                            function () use ($pdf) {
                                echo $pdf->output();
                            },
                            strtoupper("{$record->last_name}-{$record->first_name}.pdf")
                        );
                    }),
                Actions\Action::make('gwa_cert')
                    ->visible(fn(Students $record) => $record->status !== 'unverified')
                    ->label('General Weighted Average Certification')
                    ->icon('heroicon-o-document-text')
                    // ->modalHeading('Enter Receipt Details')
                    ->modalSubmitActionLabel('Generate GWA Certificate')
                    ->form([
                        Section::make('Enter Receipt Details')
                            ->schema([
                                TextInput::make('or_number')
                                    ->label('Official Receipt Number')
                                    ->required(),
                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->required(),
                                DatePicker::make('date_of_or')
                                    ->label('Date')
                                    ->required(),
                            ])
                    ])
                    ->action(function (array $data, Students $record) {
                        Receipt::create([
                            'or_number' => $data['or_number'],
                            'document_requested' => 'GWA Certificate',
                            'amount' => $data['amount'],
                            'date_of_or' => $data['date_of_or'],
                            'user_id' => \Illuminate\Support\Facades\Auth::id(),
                            'date' => now(),
                            'student_id' => $record['id'],
                        ]);

                        Notification::make()
                            ->title('Generating GWA Certificate...')
                            ->info()
                            ->send();
                        $record->load([
                            'records.curricula.programMajor.program',
                            'graduationInfos'
                        ]);

                        $pdf = PDF::loadView('GWA-Cert', [
                            'student' => $record,
                        ]);

                        $pdf->setPaper('letter', 'portrait');
                        $options = new \Dompdf\Options();
                        $options->set('defaultFont', 'Arial');
                        return response()->streamDownload(
                            function () use ($pdf) {
                                echo $pdf->output();
                            },
                            strtoupper("GWA-CERTIFICATION-{$record->last_name},{$record->first_name}.pdf")
                        );
                    }),

                Actions\Action::make('our_cert')
                    ->visible(fn(Students $record) => $record->status !== 'unverified')
                    ->label('Certificate of Graduation')
                    ->icon('heroicon-o-document-text')
                    ->tooltip('Graduation Certification')
                    // ->modalHeading('Enter Receipt Details')
                    ->modalSubmitActionLabel('Generate Graduation Certificate')
                    ->form([
                        Section::make('Enter Receipt Details')
                            ->schema([
                                TextInput::make('or_number')
                                    ->label('Official Receipt Number')
                                    ->required(),
                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->required(),
                                DatePicker::make('date_of_or')
                                    ->label('Date')
                                    ->required(),
                            ])
                    ])
                    ->action(function (array $data, Students $record) {
                        Receipt::create([
                            'or_number' => $data['or_number'],
                            'document_requested' => 'Graduation Certificate',
                            'amount' => $data['amount'],
                            'date_of_or' => $data['date_of_or'],
                            'user_id' => \Illuminate\Support\Facades\Auth::id(),
                            'date' => now(),
                            'student_id' => $record['id'],
                        ]);

                        Notification::make()
                            ->title('Generating Graduation Certificate...')
                            ->info()
                            ->send();
                        $record->load([
                            'records.curricula.programMajor.program',
                            'graduationInfos'
                        ]);

                        $pdf = PDF::loadView('OUR-Cert', [
                            'student' => $record,
                        ]);


                        $pdf->setPaper('letter', 'portrait');
                        $options = new \Dompdf\Options();
                        $options->set('defaultFont', 'Arial');
                        return response()->streamDownload(
                            function () use ($pdf) {
                                echo $pdf->output();
                            },
                            strtoupper("GRADUATION-CERTIFICATION-{$record->last_name},{$record->first_name}.pdf")
                        );
                    }),
                Actions\Action::make('gmc-college_cert')
                    ->visible(fn(Students $record) => $record->status !== 'unverified')
                    ->label('Good Moral Character Certification')
                    ->icon('heroicon-o-document-text')
                    // ->modalHeading('Enter Receipt Details')
                    ->modalSubmitActionLabel('Generate GMC Certificate')
                    ->form([
                        Section::make('Enter Receipt Details')
                            ->schema([
                                TextInput::make('or_number')
                                    ->label('Official Receipt Number')
                                    ->required(),
                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->required(),
                                DatePicker::make('date_of_or')
                                    ->label('Date')
                                    ->required(),
                            ])
                    ])
                    ->action(function (array $data, Students $record) {
                        Receipt::create([
                            'or_number' => $data['or_number'],
                            'document_requested' => 'GMC Certificate',
                            'amount' => $data['amount'],
                            'date_of_or' => $data['date_of_or'],
                            'user_id' => \Illuminate\Support\Facades\Auth::id(),
                            'date' => now(),
                            'student_id' => $record['id'],
                        ]);

                        Notification::make()
                            ->title('Generating GMC Certificate...')
                            ->info()
                            ->send();
                        $record->load([
                            'records.curricula.programMajor.program',
                            'graduationInfos'
                        ]);

                        $pdf = PDF::loadView('GMC-College-Cert', [
                            'student' => $record,
                        ]);

                        $pdf->setPaper('letter', 'portrait');
                        $options = new \Dompdf\Options();
                        $options->set('defaultFont', 'Arial');
                        return response()->streamDownload(
                            function () use ($pdf) {
                                echo $pdf->output();
                            },
                            strtoupper("GMC-CERTIFICATION-{$record->last_name},{$record->first_name}.pdf")
                        );
                    }),
                Actions\Action::make('tertiary-moi')
                    ->visible(fn(Students $record) => $record->status !== 'unverified')
                    ->label('Tertiary - Medium of Instruction Certification')
                    ->icon('heroicon-o-document-text')
                    // ->modalHeading('Enter Receipt Details')
                    ->modalSubmitActionLabel('Generate Tertiary MOI Certificate')
                    ->form([
                        Section::make('Enter Receipt Details')
                            ->schema([
                                TextInput::make('or_number')
                                    ->label('Official Receipt Number')
                                    ->required(),
                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->required(),
                                DatePicker::make('date_of_or')
                                    ->label('Date')
                                    ->required(),
                            ])
                    ])
                    ->action(function (array $data, Students $record) {
                        Receipt::create([
                            'or_number' => $data['or_number'],
                            'document_requested' => 'Tertiary MOI Certificate',
                            'amount' => $data['amount'],
                            'date_of_or' => $data['date_of_or'],
                            'user_id' => \Illuminate\Support\Facades\Auth::id(),
                            'date' => now(),
                            'student_id' => $record['id'],
                        ]);

                        Notification::make()
                            ->title('Generating MOF Certificate...')
                            ->info()
                            ->send();
                        $record->load([
                            'records.curricula.programMajor.program',
                            'graduationInfos'
                        ]);

                        $pdf = PDF::loadView('MOI-Tertiary', [
                            'student' => $record,
                        ]);

                        $pdf->setPaper('letter', 'portrait');
                        $options = new \Dompdf\Options();
                        $options->set('defaultFont', 'Arial');
                        return response()->streamDownload(
                            function () use ($pdf) {
                                echo $pdf->output();
                            },
                            strtoupper("Tertiary-MOI-CERTIFICATION-{$record->last_name}, {$record->first_name}.pdf")
                        );
                    }),
                // Actions\Action::make('secondary-moi')
                //     ->visible(fn(Students $record) => $record->status !== 'unverified')
                //     ->label('Secondary - Medium of Instruction Certification')
                //     ->icon('heroicon-o-document-text')
                //     // ->modalHeading('Enter Receipt Details')
                //     ->modalSubmitActionLabel('Generate Secondary MOI Certificate')
                //     ->form([
                //         Section::make('Enter Receipt Details')
                //             ->schema([
                //                 TextInput::make('or_number')
                //                     ->label('Official Receipt Number')
                //                     ->required(),
                //                 TextInput::make('amount')
                //                     ->label('Amount')
                //                     ->required(),
                //                 DatePicker::make('date_of_or')
                //                     ->label('Date')
                //                     ->required(),
                //             ])
                //     ])
                //     ->action(function (array $data, Students $record) {
                //         Receipt::create([
                //             'or_number' => $data['or_number'],
                //             'document_requested' => 'Secondary MOI Certificate',
                //             'amount' => $data['amount'],
                //             'date_of_or' => $data['date_of_or'],
                //             'user_id' => \Illuminate\Support\Facades\Auth::id(),
                //             'date' => now(),
                //             'student_id' => $record['id'],
                //         ]);

                //         Notification::make()
                //             ->title('Generating MOI Certificate...')
                //             ->info()
                //             ->send();
                //         $record->load([
                //             'records.curricula.programMajor.program',
                //             'graduationInfos'
                //         ]);

                //         $pdf = PDF::loadView('MOI-Secondary', [
                //             'student' => $record,
                //         ]);

                //         $pdf->setPaper('letter', 'portrait');
                //         $options = new \Dompdf\Options();
                //         $options->set('defaultFont', 'Arial');
                //         return response()->streamDownload(
                //             function () use ($pdf) {
                //                 echo $pdf->output();
                //             },
                //             strtoupper("Secondary-MOI-CERTIFICATION-{$record->last_name}, {$record->first_name}.pdf")
                //         );
                //     }),
                Actions\Action::make('honorable-dismissal')
                    ->visible(fn(Students $record) => $record->status !== 'unverified')
                    ->label('Honorable Dismissal')
                    ->icon('heroicon-o-document-text')
                    // ->modalHeading('Enter Receipt Details')
                    ->modalSubmitActionLabel('Generate Honorable Dismissal')
                    ->form([
                        Section::make('Enter Receipt Details')
                            ->schema([
                                TextInput::make('or_number')
                                    ->label('Official Receipt Number')
                                    ->required(),
                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->required(),
                                DatePicker::make('date_of_or')
                                    ->label('Date')
                                    ->required(),
                            ])
                    ])
                    ->action(function (array $data, Students $record) {
                        Receipt::create([
                            'or_number' => $data['or_number'],
                            'document_requested' => 'Honorable Dismissal',
                            'amount' => $data['amount'],
                            'date_of_or' => $data['date_of_or'],
                            'user_id' => \Illuminate\Support\Facades\Auth::id(),
                            'date' => now(),
                            'student_id' => $record['id'],
                        ]);

                        Notification::make()
                            ->title('Generating Honorable Dismissal...')
                            ->info()
                            ->send();
                        $record->load([
                            'records.curricula.programMajor.program',
                            'graduationInfos'
                        ]);

                        $pdf = PDF::loadView('Honorable-Dismissal', [
                            'student' => $record,
                        ]);

                        $pdf->setPaper('letter', 'portrait');
                        $options = new \Dompdf\Options();
                        $options->set('defaultFont', 'Arial');
                        return response()->streamDownload(
                            function () use ($pdf) {
                                echo $pdf->output();
                            },
                            strtoupper("Honorable Dismissal - {$record->last_name}, {$record->first_name}.pdf")
                        );
                    }),
                Actions\Action::make('diploma')
                    ->visible(fn(Students $record) => $record->status !== 'unverified')
                    ->label('Diploma')
                    ->icon('heroicon-o-document-text')
                    // ->modalHeading('Enter Receipt Details')
                    ->modalSubmitActionLabel('Generate Diploma')
                    ->form([
                        Section::make('Enter Receipt Details')
                            ->schema([
                                TextInput::make('or_number')
                                    ->label('Official Receipt Number')
                                    ->required(),
                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->required(),
                                DatePicker::make('date_of_or')
                                    ->label('Date')
                                    ->required(),
                            ])
                    ])
                    ->action(function (array $data, Students $record) {
                        Receipt::create([
                            'or_number' => $data['or_number'],
                            'document_requested' => 'Honorable Dismissal',
                            'amount' => $data['amount'],
                            'date_of_or' => $data['date_of_or'],
                            'user_id' => \Illuminate\Support\Facades\Auth::id(),
                            'date' => now(),
                            'student_id' => $record['id'],
                        ]);

                        Notification::make()
                            ->title('Generating Diploma...')
                            ->info()
                            ->send();
                        $record->load([
                            'records.curricula.programMajor.program',
                            'graduationInfos'
                        ]);

                        $pdf = PDF::loadView('Honorable-Dismissal', [
                            'student' => $record,
                        ]);

                        $pdf->setPaper('letter', 'portrait');
                        $options = new \Dompdf\Options();
                        $options->set('defaultFont', 'Arial');
                        return response()->streamDownload(
                            function () use ($pdf) {
                                echo $pdf->output();
                            },
                            strtoupper("Honorable Dismissal - {$record->last_name}, {$record->first_name}.pdf")
                        );
                    }),
            ])
            
                ->icon('heroicon-o-printer')
                ->label('Print Documents')
                ->dropdownWidth('lg')
                ->button(),

            Actions\Action::make('view-attachment')
                ->label('View Attachment')
                ->icon('heroicon-o-paper-clip')
                ->url(function (Students $record) {
                    // Use Str::slug instead of str_slug
                    $firstName = ucfirst($record->first_name ?? '');
                    $lastName = ucfirst($record->last_name ?? 'student');
                    $studentName = Str::slug("$firstName $lastName");
                    return route('view-attachment', [
                        'studentId' => $record->id,
                        'studentName' => $studentName
                    ]);
                })
                ->openUrlInNewTab()
                ->visible(function (Students $record) {
                    return $record->records()
                        ->whereNotNull('attachment')
                        ->where('attachment', '!=', '')
                        ->exists();
                })
        ];
    }
}