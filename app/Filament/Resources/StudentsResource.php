<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentsResource\Pages;
use App\Filament\Resources\StudentsResource\RelationManagers;
use App\Models\Students;
use App\Models\AcadTerms;
use App\Models\AcadYears;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Closure;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use App\Models\StudentsGraduationInfos;
use App\Models\StudentsRecords;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Forms\Components\Toggle;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\HasManyRepeater;
use App\Models\Courses;
use App\Models\Curricula;
use App\Models\Programs;
use App\Models\Campuses;
use App\Models\Colleges;
use App\Models\ProgramsMajor;
use App\Filament\Resources\StudentsResource\Pages\EditStudents;
use Filament\Infolists\Components\Tabs;
use Illuminate\Validation\Rule;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\FileUpload;


class StudentsResource extends Resource
{
    protected static ?string $model = Students::class;
    protected static ?string $navigationGroup = 'Student Information';

    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationIcon = 'heroicon-s-user';

    public static function form(Form $form): Form
    {
        return $form
            // Main student information section - table: students
            ->schema([
                Section::make('General Student Information')
                    ->description("Enter the student's general information.")
                    ->schema([
                        Grid::make(4)->schema([
                            TextInput::make('last_name')->label("Last Name"),
                            TextInput::make('first_name')->label("First Name"),
                            TextInput::make('middle_name')->label("Middle Name"),
                            TextInput::make('suffix')->label("Suffix"),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('sex')->label('Sex')->options([
                                'M' => 'Male',
                                'F' => 'Female',
                            ])->required(),
                            DatePicker::make('birthdate')->label("Date of Birth")->required(),
                        ]),
                        TextInput::make('address')->label("Address")->required(),
                        DatePicker::make('birthdate')->label("Date of Birth")->required(),
                        TextInput::make('birthplace')->label('Place of Birth')->required(),

                        Grid::make(3)->schema([
                            Select::make('region')
                                ->label("Region")
                                ->options(function () {
                                    // Fetch regions from the JSON file with SSL verification disabled
                                    $response = Http::withOptions(['verify' => false])->get('https://raw.githubusercontent.com/isaacdarcilla/philippine-addresses/master/region.json');

                                    if (!$response->successful()) {
                                        return [];
                                    }

                                    $regions = $response->json();

                                    if (!is_array($regions) || empty($regions)) {
                                        return [];
                                    }

                                    // Transform the response to match the format required by the Select component
                                    return collect($regions)->pluck('region_name', 'region_code');
                                })
                                ->required()
                                ->reactive(),

                            Select::make('province')
                                ->label("Province")
                                ->options(function (callable $get) {
                                    $regionCode = $get('region');

                                    if (!$regionCode) {
                                        return [];
                                    }

                                    // Fetch provinces from the JSON file with SSL verification disabled
                                    $response = Http::withOptions(['verify' => false])->get('https://raw.githubusercontent.com/isaacdarcilla/philippine-addresses/master/province.json');
                                    $provinces = $response->json();

                                    if (!$response->successful()) {
                                        return [];
                                    }

                                    // Filter provinces based on selected region
                                    $filteredProvinces = collect($provinces)->filter(function ($province) use ($regionCode) {
                                        return $province['region_code'] === $regionCode;
                                        // return $province['region_name'] === $regionName;
                                    });

                                    // Transform the response to match the format required by the Select component
                                    return $filteredProvinces->pluck('province_name', 'province_code');
                                })
                                ->required()
                                ->reactive(),

                            Select::make('city_municipality')
                                ->label("City/Municipality")
                                ->options(function (callable $get) {
                                    $provinceCode = $get('province'); // Get the selected province_code

                                    if (!$provinceCode) {
                                        return [];
                                    }

                                    // Fetch cities from the JSON file with SSL verification disabled
                                    $response = Http::withOptions(['verify' => false])
                                        ->get('https://raw.githubusercontent.com/isaacdarcilla/philippine-addresses/master/city.json');

                                    if (!$response->successful()) {
                                        return [];
                                    }

                                    $cities = $response->json();

                                    if (!is_array($cities) || empty($cities)) {
                                        return [];
                                    }

                                    // Filter cities based on selected province_code
                                    $filteredCities = collect($cities)->filter(function ($city_municipality) use ($provinceCode) {
                                        return $city_municipality['province_code'] === $provinceCode;
                                        return $city_municipality['province_name'] === $provinceCode;
                                    });

                                    // Store `city_code` in DB but show `city_name` in UI
                                    return $filteredCities->pluck('city_name', 'city_code');
                                })
                                ->required(),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('gwa')->label('General Weighted Average')->required(),
                            TextInput::make('nstp_number')->label('NSTP Number')->required(),
                        ]),
                    ]),

                // Student's graduation information section - table: students_graduation_infos
                Section::make('Student Graduation Information')
                    ->relationship('graduationInfos')
                    ->description("Enter the student's graduation information.")
                    ->schema([
                        Grid::make(3)->schema([
                            DatePicker::make('graduation_date')->label('Date of Graduation')->required(),
                            TextInput::make('board_approval')->label('Special Order Number (Board Resolution)')->required(),
                            Select::make('latin_honor')->label('Latin Honor')->options([
                                'Cum Laude' => 'Cum Laude',
                                'Magna Cum Laude' => 'Magna Cum Laude',
                                'Summa Cum Laude' => 'Summa Cum Laude',
                                'Academic Distinction' => 'Academic Distinction',
                                'With Honor' => 'With Honor',
                                'With High Honor' => 'With High Honor',
                                'With Highest Honor' => 'With Highest Honor',
                            ]),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('degree_attained')->label('Degree Attained')->options([
                                "Bachelor's Degree" => "Bachelor's Degree",
                                "Master's Degree" => "Master's Degree",
                                'Doctorate Degree' => 'Doctorate Degree',
                            ]),
                            TextInput::make('dates_of_attendance')->label('Dates of Attendance (Month Year - Month Year)')->required(),
                        ]),
                        TextInput::make('dates_of_attendance')->label('Dates of Attendance (Month Year - Month Year)')->required(),
                    ]),

                // Student's registration information section - table: students_registration_infos
                Section::make('Student Registration Information')
                    ->relationship('registrationInfos')
                    ->description("Enter the student's registration information.")
                    ->schema([
                        TextInput::make('last_school_attended')->required()->label('Last School Attended (High School/College)'),
                        Grid::make(2)->schema([
                            TextInput::make('last_year_attended')->label('Last Year Attended (Date graduated/last attended)')->required()->maxLength(4)->numeric(),
                            Select::make('category')
                                ->label('Category')
                                ->options([
                                    'Transferee' => 'Transferee',
                                    'High School Graduate' => 'High School Graduate',
                                    'Senior High School Graduate' => 'Senior High School Graduate',
                                    'College Graduate' => 'College Graduate',
                                    'Others' => 'Others',
                                ])
                                ->required()
                                ->reactive(),
                            TextInput::make('other_category')
                                ->label('Specify Other Category')
                                ->required(fn($get) => $get('category') === 'Others')
                                ->visible(fn($get) => $get('category') === 'Others'),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('acad_year_id')
                                ->label('Select Academic Year')
                                ->required()
                                ->options(AcadYears::all()->pluck('year', 'id'))
                                ->searchable()
                                ->reactive()
                                ->hidden(fn($livewire) => $livewire instanceof Pages\EditStudents)
                                ->getSearchResultsUsing(
                                    fn(string $query) => AcadYears::where('year', 'like', "%{$query}%")->get()->pluck('year', 'id')
                                )
                                ->getOptionLabelUsing(fn($value) => AcadYears::find($value)?->year ?? 'Unknown Year'),
                            Select::make('acad_term_id')
                                ->label('Select Academic Term (Date/Semester admitted)')
                                ->required()
                                ->reactive()
                                ->options(
                                    function ($get) {
                                        $acadYearId = $get('acad_year_id');
                                        if ($acadYearId) {
                                            return AcadTerms::where('acad_year_id', $acadYearId)->pluck('acad_term', 'id');
                                        }
                                        return [];
                                    }
                                )
                                ->searchable()
                                ->getSearchResultsUsing(
                                    fn(string $query) => AcadTerms::where('acad_term', 'like', "%{$query}%")->get()->pluck('acad_term', 'id')
                                )
                                ->getOptionLabelUsing(fn($value) => AcadTerms::find($value)?->acad_term ?? 'Unknown Academic Term'),

                ]),

            ]),

                Section::make('Ladderized Graduation Information')
                ->schema([
                    Repeater::make('ladderized')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('board_approval')
                            ->label('Board Approval'),
                            Select::make('latin_honor')
                            ->label('Honor')
                            ->options([
                                'With Honor' => 'With Honor',
                                'With High Honor' => 'With High Honor',
                                'With Highest Honor' => 'With Highest Honor',
                            ]),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('program_cert')
                            ->label('Program Certificate'),
                            DatePicker::make('graduation_date')
                            ->label('Graduation Date'),
                        ]),
                    ])
                        ->disableItemCreation(fn($livewire) => $livewire instanceof EditStudents),
                ]),

                Toggle::make('is_regular')->label('Regular Student')->default(true)->reactive()->hidden(fn($livewire) => $livewire instanceof EditStudents),
                // Student's grades - table: students_records
                Section::make('Student Records (Regular)')
                ->visible(fn($get) => $get('is_regular'))
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('campus_id')->label('Select Campus')->required()->reactive()
                            ->options(
                                Campuses::all()->pluck('campus_name', 'id')
                            )->afterStateUpdated(function ($set) {
                                $set('college_id', null);
                                $set('program_id', null);
                                $set('program_major_id', null);
                            })->searchable(),

                        Select::make('college_id')->label('Select College')->required()->reactive()->options(
                            function ($get) {
                                $campus_id = $get('campus_id');
                                if ($campus_id) {
                                    return Colleges::where('campus_id', $campus_id)->pluck('college_name', 'id');
                                }
                                return [];
                            }
                        )->searchable(),
                    ]),
                    Grid::make(2)->schema([
                        Select::make('program_id')->label('Select Program')->required()->reactive()->options(
                            function ($get) {
                                $college_id = $get('college_id');
                                if ($college_id) {
                                    return Programs::where('college_id', $college_id)->pluck('program_name', 'id');
                                }
                                return [];
                            }
                        )->searchable()->getOptionLabelUsing(fn($value) => Programs::find($value)?->program_name ?? 'Unknown Program'),

                        Select::make('program_major_id')->label('Select Program Major')->reactive()->options(
                            function ($get) {
                                $program_id = $get('program_id');
                                if ($program_id) {
                                    return ProgramsMajor::where('program_id', $program_id)->pluck('program_major_name', 'id');
                                }
                                return [];
                            }
                        )->searchable()->getOptionLabelUsing(fn($value) => ProgramsMajor::find($value)?->program_major_name ?? 'Unknown Major'),
                    ]),



                    Repeater::make('records_regular')->label('Curriculum')
                    ->schema([
                        Select::make('curricula_id')->label('Select Curriculum')->required()->reactive()->options(
                            function ($get) {
                                $program_id = $get('../../program_id');
                                $program_major_id = $get('../../program_major_id');
                                if ($program_id && $program_major_id) {
                                    return Curricula::where('program_id', $program_id)
                                        ->where('program_major_id', $program_major_id)
                                        ->pluck('curricula_name', 'id');
                                } elseif ($program_id) {
                                    return Curricula::where('program_id', $program_id)->pluck('curricula_name', 'id');
                                }
                                return [];
                            }
                        )->searchable()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $courses = Courses::where('curricula_id', $state)->get();
                                    $set('records_regular_grades', $courses->map(function ($course) {
                                        return [
                                            'course_code' => $course->course_code,
                                            'descriptive_title' => $course->descriptive_title,
                                            'course_unit' => $course->course_unit,
                                        ];
                                    })->toArray());
                                }
                            }),

                        TableRepeater::make('records_regular_grades')
                        ->label('Courses & Grades')
                        ->reactive()
                            ->headers([
                                Header::make('course_code')->label('Course Code')->width('120px'),
                                Header::make('descriptive_title')->label('Descriptive Title')->width('300px'),
                                Header::make('final_grade')->label('Final Grade')->width('80px'),
                                Header::make('removal_rating')->label('Removal Rating')->width('80px'),
                                Header::make('course_unit')->label('Units of Credit')->width('60px'),
                            ])
                            ->schema([
                                Select::make('course_code')
                                ->label('Course Code')
                                ->required()
                                    ->options(
                                        function ($get) {
                                            $curricula_id = $get('../../curricula_id'); // Ensure correct path
                                            if ($curricula_id) {
                                                return Courses::where('curricula_id', $curricula_id)
                                                    ->pluck('course_code', 'id'); // Fetch courses based on curriculum
                                            }
                                            return [];
                                        }
                                    )
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $course = Courses::find($state);
                                            if ($course) {
                                                $set('descriptive_title', $course->descriptive_title);
                                                $set('course_unit', $course->course_unit);
                                            }
                                        }
                                    }),
                                Hidden::make('course_code'),
                                TextInput::make('descriptive_title')->label('Descriptive Title')->disabled(),
                                TextInput::make('final_grade')->label('Final Grade')->required()->maxLength(255),
                                TextInput::make('removal_rating')->label('Removal Rating'),
                                TextInput::make('course_unit')->label('Units of Credit')->disabled(),
                            ])
                            ->disableItemCreation(fn($livewire) => $livewire instanceof EditStudents),
                    ])
                        ->disableItemCreation(fn($livewire) => $livewire instanceof EditStudents),

                ]),
            Section::make('Student Records (Irregular)')
            ->visible(fn($get) => !$get('is_regular'))
                ->schema([
                    FileUpload::make('document')
                        ->label('Upload Document/s')
                        ->maxSize(10240)
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->helperText('Maximum file size: 10MB. Accepted file types: PDF and images. File attachement is applicable only for Transferees.')
                        ->columnSpanFull(),

                    Repeater::make('campus_college_records')
                    ->label('Add Campus/College Records')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('campus_id')
                                ->label('Select Campus')
                                ->required()
                                ->reactive()
                                ->options(Campuses::all()->pluck('campus_name', 'id'))
                                ->afterStateUpdated(function ($set) {
                                    $set('college_id', null);
                                    $set('program_id', null);
                                    $set('program_major_id', null);
                                })
                                ->searchable(),

                            Select::make('college_id')
                                ->label('Select College')
                                ->required()
                                ->reactive()
                                ->options(function ($get) {
                                    $campus_id = $get('campus_id');
                                    return $campus_id ? Colleges::where('campus_id', $campus_id)->pluck('college_name', 'id') : [];
                                })
                                ->searchable(),
                        ]),

                        Grid::make(2)->schema([
                            Select::make('program_id')
                                ->label('Select Program')
                                ->required()
                                ->reactive()
                                ->options(fn($get) => Programs::where('college_id', $get('college_id'))->pluck('program_name', 'id') ?? [])
                                ->searchable()
                                ->getOptionLabelUsing(fn($value) => Programs::find($value)?->program_name ?? 'Unknown Program'),

                            Select::make('program_major_id')
                            ->label('Select Program Major')
                            ->reactive()
                                ->options(fn($get) => ProgramsMajor::where('program_id', $get('program_id'))->pluck('program_major_name', 'id') ?? [])
                                ->searchable()
                                ->getOptionLabelUsing(fn($value) => ProgramsMajor::find($value)?->program_major_name ?? 'Unknown Major'),
                        ]),

                        Repeater::make('records_irregular')
                            ->label('Curriculum')
                            ->schema([
                                Select::make('curricula_id')
                                    ->label('Select Curriculum')
                                    ->required()
                                    ->reactive()
                                    ->options(function ($get) {
                                        $program_id = $get('../../program_id');
                                        $program_major_id = $get('../../program_major_id');
                                        return Curricula::where('program_id', $program_id)
                                            ->when($program_major_id, fn($q) => $q->where('program_major_id', $program_major_id))
                                            ->pluck('curricula_name', 'id') ?? [];
                                    })
                                    ->searchable()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $courses = Courses::where('curricula_id', $state)->get();
                                            $set('records_regular_grades', $courses->map(fn($course) => [
                                                'course_code' => $course->course_code,
                                                'descriptive_title' => $course->descriptive_title,
                                                'course_unit' => $course->course_unit,
                                                'is_preloaded' => true,
                                            ])->toArray());
                                        }
                                    }),

                            TableRepeater::make('records_regular_grades')
                                ->label('Courses & Grades')
                                ->reactive()
                                ->headers([
                                    Header::make('course_code')->label('Course Code')->width('120px'),
                                    Header::make('descriptive_title')->label('Descriptive Title')->width('300px'),
                                    Header::make('final_grade')->label('Final Grade')->width('80px'),
                                    Header::make('removal_rating')->label('Removal Rating')->width('80px'),
                                    Header::make('course_unit')->label('Units of Credit')->width('60px'),
                                ])
                                ->schema([
                                    TextInput::make('course_code')
                                        ->label('Course Code')
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            if ($state) {
                                                $course = Courses::where('course_code', $state)->first();
                                                if ($course) {
                                                    $set('descriptive_title', $course->descriptive_title);
                                                    $set('course_unit', $course->course_unit);
                                                    $set('is_custom_course', false);
                                                    $set('course_id', $course->id); // Save the course ID
                                                } else {
                                                    // If course not found, clear related fields
                                                    $set('descriptive_title', null);
                                                    $set('course_unit', null);
                                                    $set('course_id', null);
                                                }
                                            }
                                        })
                                        ->datalist(function () {
                                            return Courses::pluck('course_code')->toArray();
                                        })
                                        ->disabled(fn($get) => $get('is_preloaded'))
                                        ->dehydrated(true)
                                        ->validationAttribute('course code')
                                        ->exists('courses', 'course_code'),

                                    // Add hidden fields to store additional course data
                                    Hidden::make('course_code'),

                                    TextInput::make('descriptive_title')
                                        ->label('Descriptive Title')
                                        ->required(fn($get) => $get('is_custom_course'))
                                        ->disabled(fn($get) => $get('is_preloaded')),

                                    TextInput::make('final_grade')
                                        ->label('Final Grade')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('removal_rating')
                                        ->label('Removal Rating'),

                                    TextInput::make('course_unit')
                                        ->label('Units of Credit')
                                        ->required(fn($get) => $get('is_custom_course'))
                                        ->disabled(fn($get) => $get('is_preloaded')),
                                ])
                                ->defaultItems(1)
                                ->addActionLabel('Add New Courses & Grades')
                                ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                    return array_merge($data, ['is_preloaded' => false]);
                                }),
                        ]),
                    ])
                        ->addActionLabel('Add New Campus/College')
                        ->collapsible()
                        ->disableItemCreation(fn($livewire) => $livewire instanceof EditStudents),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = Filament::auth()->user();
        return $table
        ->query(
            Students::query()
                ->when(
                    !$user->roles->contains('name', 'super_admin'), // If NOT super admin
                    fn($query) => $query->whereNull('students.deleted_at') // Specify the table explicitly
                )
        )
            ->columns([
            TextColumn::make('name')
                ->label('Name')
                ->getStateUsing(
                    fn($record) =>
                    "{$record->first_name}" .
                    ($record->middle_name ? " {$record->middle_name}" : "") .
                    " {$record->last_name}" .
                    ($record->suffix ? " {$record->suffix}" : "")
                )
                ->sortable(query: function ($query, $direction) {
                    $query->orderBy('first_name', $direction)
                    ->orderBy('middle_name', $direction)
                    ->orderBy('last_name', $direction);
                })
                ->searchable(query: function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('suffix', 'like', "%{$search}%");
                    });
                }),

            TextColumn::make('program')
                ->label('Program')
                ->getStateUsing(function ($record) {
                    // Get the latest record for this student
                    $studentRecord = $record->records()->latest()->first();
                    if (!$studentRecord) {
                        return 'N/A';
                    }

                    $curricula = $studentRecord->curricula;  // Use the relationship
                    return $curricula && str_contains($curricula->curricula_name, ',')
                        ? trim(explode(',', $curricula->curricula_name)[1])
                        : ($curricula ? $curricula->curricula_name : 'N/A');
                })
                ->sortable(query: function ($query, $direction) {
                    $query->leftJoin(
                        'students_records as sr',
                        'students.id',
                        '=',
                        'sr.student_id'
                    )
                        ->leftJoin('curriculas as c1', 'sr.curricula_id', '=', 'c1.id')
                        ->orderBy('c1.curricula_name', $direction);
                })
                ->searchable(query: function ($query, $search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->whereHas('records', function ($recordQuery) use ($search) {
                            $recordQuery->whereHas('curricula', function ($curriculaQuery) use ($search) {
                                $curriculaQuery->where('curricula_name', 'like', "%{$search}%");
                            });
                        });
                    });
                }),


            TextColumn::make('major')
                ->label('Major')
                ->getStateUsing(function ($record) {
                    // Get the latest record for this student
                    $studentRecord = $record->records()->latest()->first();
                    if (!$studentRecord) {
                        return 'N/A';
                    }

                    $curricula = $studentRecord->curricula;
                    return $curricula && $curricula->programMajor
                        ? $curricula->programMajor->program_major_name
                        : 'N/A';
                })
                ->sortable(query: function ($query, $direction) {
                    $query->leftJoin('students_records as sr2', 'students.id', '=', 'sr2.student_id')
                        ->leftJoin('curriculas as c2', 'sr2.curricula_id', '=', 'c2.id')
                        ->leftJoin('programs_majors as pm', 'c2.program_major_id', '=', 'pm.id')
                        ->orderBy('pm.program_major_name', $direction);
                })
                ->searchable(query: function ($query, $search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->whereHas('records', function ($recordQuery) use ($search) {
                            $recordQuery->whereHas('curricula', function ($curriculaQuery) use ($search) {
                                $curriculaQuery->whereHas('programMajor', function ($majorQuery) use ($search) {
                                    $majorQuery->where('program_major_name', 'like', "%{$search}%");
                                });
                            });
                        });
                    });
                }),

            TextColumn::make('graduation_date')
            ->label('Date of Graduation')
            ->getStateUsing(function ($record) {
                $graduationInfo = $record->graduationInfos()->first();
                return $graduationInfo && $graduationInfo->graduation_date
                    ? date('F d, Y', strtotime($graduationInfo->graduation_date))
                    : 'N/A';
            })
                ->sortable()
                ->searchable(query: function ($query, $search) {
                    $query->leftJoin('students_graduation_infos', 'students.id', '=', 'students_graduation_infos.student_id')
                    ->where('students_graduation_infos.graduation_date', 'like', "%{$search}%");
                }),
                //     TextColumn::make('status')
                //     ->label('Status')
                //     ->badge() // Turns text into a badge
                //     ->formatStateUsing(fn (string $state): string => strtoupper($state))
                //     ->color(fn (string $state): string => match ($state) {
                //         'verified' => 'success',   // Green badge for verified
                //         'unverified' => 'danger',  // Red badge for unverified
                //         default => 'gray',         // Default gray badge
                // }),
                // ])   
                IconColumn::make('status')
                    ->label('Status')
                    ->icon(fn (string $state): string => match ($state) {
                        'verified' => 'heroicon-o-check-circle', 
                        'unverified' => 'heroicon-o-x-circle',    
                        default => 'heroicon-o-question-mark-circle', 
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'verified' => 'info',  
                        'unverified' => 'danger',  
                        default => 'gray',         
                    })
                    ->tooltip(fn (string $state): string => match ($state) {
                        'verified' => 'Verified',
                        'unverified' => 'Unverified',
                        default => 'Unknown status',
                    })
                ])

            ->defaultSort('name')
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->select('students.*')
                    ->distinct()
                    ->leftJoin('curriculas', 'students.curriculum_id', '=', 'curriculas.id')
                    ->leftJoin('programs_majors', 'curriculas.program_major_id', '=', 'programs_majors.id')
                    ->leftJoin('students_graduation_infos', 'students.id', '=', 'students_graduation_infos.student_id');
            })
            ->emptyStateIcon('heroicon-s-user')
            ->emptyStateHeading('Student Not Available')
            ->emptyStateDescription('There are currently no students in the system.')
            ->filters([
                // Show the "Trashed" filter ONLY if the user is a super admin
                ...($user->roles->contains('name', 'super_admin') 
                ? [Tables\Filters\TrashedFilter::make()] 
                : [])
            ])     
            ->filtersTriggerAction(fn (\Filament\Tables\Actions\Action $action) => 
                $action->icon('heroicon-o-adjustments-vertical')
            )    
            ->actions([
            // Tables\Actions\EditAction::make(),
            // Tables\Actions\DeleteAction::make(),

            Tables\Actions\ViewAction::make()
                ->iconButton()
                ->icon('heroicon-o-eye')
                ->tooltip('View Record'),

            Tables\Actions\EditAction::make()
                ->iconButton()
                ->icon('heroicon-o-pencil-square')
                ->tooltip('Edit Record'),

            Tables\Actions\DeleteAction::make()
                ->iconButton()
                ->icon('heroicon-o-trash')
                ->tooltip('Delete Record'),
                    // Show "Restore" button only for super_admin
                ...($user->roles->contains('name', 'super_admin') ? [Tables\Actions\RestoreAction::make()] : [])
                ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudents::route('/create'),
            'edit' => Pages\EditStudents::route('/{record}/edit'),
        ];
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ];
    }

    // protected function afterSave($record)
    // {
    //     StudentsGraduationInfos::create([
    //         'student_id' => $record->id,
    //         'graduation_date' => $record->graduation_date,
    //         'board_approval' => $record->board_approval,
    //         'latin_honor' => $record->latin_honor,
    //         'degree_attained' => $record->degree_attained,
    //         'dates_of_attendance' => $record->dates_of_attendance,
    //     ]);
    // }
}

// table repeater code segment for student grades
// TableRepeater::make('records')
                        //     ->relationship('records')
                        //     ->headers([
                        //         Header::make('Term')->width('150px'),
                        //         Header::make('Course Code')->width('150px'),
                        //         Header::make('Descriptive Title')->width('150px'),
                        //         Header::make('Final Grades')->width('150px'),
                        //         Header::make('Removal Rating')->width('150px'),
                        //         Header::make('Units of Credit')->width('150px'),
                        //     ])
                        //     ->schema([
                        //         Select::make('term_id')
                        //             ->label('Academic Term')
                        //             ->options(AcadTerms::all()->pluck('acad_term', 'id'))
                        //             ->searchable()
                        //             ->getSearchResultsUsing(fn (string $query) => AcadTerms::where('acad_term', 'like', "%{$query}%")->get()->pluck('acad_term', 'id'))
                        //             ->getOptionLabelUsing(fn ($value) => AcadTerms::find($value)?->acad_term ?? 'Unknown Academic Term')
                        //             ->required()
                        //             ->reactive(),
                        //         Select::make('course_id')
                        //             ->label('Course Code')
                        //             ->options(Courses::all()->pluck('course_code', 'id'))
                        //             ->searchable()
                        //             ->getSearchResultsUsing(fn (string $query) => Courses::where('course_code', 'like', "%{$query}%")->get()->pluck('course_code', 'id'))
                        //             ->getOptionLabelUsing(fn ($value) => Courses::find($value)?->course_code ?? 'Unknown Course Code')
                        //             ->required()
                        //             ->reactive()
                        //         ])
                        //     ->columnSpan('full')
                        // ]),

// REGULAR NAGANA INI FOR REFERENCE HAHAHA
// Section::make('Student Records')
// ->schema([
//     Repeater::make('student_records')
//         ->label('Student Records')
//         ->schema([
//             Select::make('campus_id')
//         ->label('Select Campus')
//         ->required()
//         ->reactive()
//         ->options(Campuses::all()->pluck('campus_name', 'id'))
//         ->searchable(),
//     Select::make('college_id')
//         ->label('Select College')
//         ->required()
//         ->reactive()
//         ->options(function ($get) {
//             $campus_id = $get('campus_id');
//             if ($campus_id) {
//                 return Colleges::where('campus_id', $campus_id)->pluck('college_name', 'id');
//             }
//             return [];
//         })
//         ->searchable(),
//     Select::make('program_id')
//         ->label('Select Program')
//         ->required()
//         ->reactive()
//         // get programs from the selected college
//         ->options(function ($get) {
//             $college_id = $get('college_id');
//             if ($college_id) {
//                 return Programs::where('college_id', $college_id)->pluck('program_name', 'id');
//             }
//             return [];
//         })
//         ->searchable()
//         ->getOptionLabelUsing(fn ($value) => Programs::find($value)?->program_name ?? 'Unknown Program'),
//     Select::make('program_major_id')
//         ->label('Select Program Major')
//         ->reactive()
//         // get program majors from the selected program
//         ->options(function ($get) {
//             $program_id = $get('program_id');
//             if ($program_id) {
//                 return ProgramsMajor::where('program_id', $program_id)->pluck('program_major_name', 'id');
//             }
//             return [];
//         })
//         ->searchable()
//         ->getOptionLabelUsing(fn ($value) => ProgramsMajor::find($value)?->program_major_name ?? 'Unknown Major'),
//     Select::make('curricula_id')
//         ->label('Select Curriculum')
//         ->required()
//         ->reactive()
//         ->options(function ($get) {
//             $program_id = $get('program_id');
//             $program_major_id = $get('program_major_id');
//             if ($program_id && $program_major_id) {
//                 return Curricula::where('program_id', $program_id)
//                     ->where('program_major_id', $program_major_id)
//                     ->pluck('curricula_name', 'id');
//             } elseif ($program_id) {
//                 return Curricula::where('program_id', $program_id)
//                     ->pluck('curricula_name', 'id');
//             }
//             return [];
//         })
//         ->searchable()
//         ->afterStateUpdated(function ($state, callable $set) {
//             if ($state) {
//                 $courses = Courses::where('curricula_id', $state)->get();
//                 $set('records', $courses->map(function ($course) {
//                     return [
//                         'course_id' => $course->id,
//                         'descriptive_title' => $course->descriptive_title,
//                         'course_unit' => $course->course_unit,
//                     ];
//                 })->toArray());
//             }
//         }),
//         Repeater::make('records')
//         ->label('Grades')
//         ->relationship('records')
//         ->schema([
//             Select::make('course_id')
//                 ->label('Course Code')
//                 ->options(function ($get) {
//                     $curricula_id = $get('curricula_id');
//                     if ($curricula_id) {
//                         return Courses::where('curricula_id', $curricula_id)->pluck('course_code', 'id');
//                     }
//                     return [];
//                 })
//                 ->reactive()
//                 ->afterStateUpdated(function ($state, callable $set) {
//                     $course = Courses::find($state);
//                     $set('descriptive_title', $course ? $course->descriptive_title : 'Unknown Descriptive Title');
//                     $set('course_unit', $course ? $course->course_unit : 'Unknown Units');
//                 })
//                 ->searchable()
//                 ->getOptionLabelUsing(fn ($value) => Courses::find($value)?->course_code ?? 'Unknown Course Code')
//                 ->disabled(),
//             TextInput::make('descriptive_title')
//                 ->label('Descriptive Title')
//                 ->disabled(),
//             TextInput::make('final_grades')
//                 ->label('Final Grades')
//                 ->required(),
//             TextInput::make('removal_rating')
//                 ->label('Removal Rating'),
//             TextInput::make('course_unit')
//                 ->label('Units of Credit')
//                 ->disabled(),
//         ])
//         ->columnSpan('full'),
// ])
// ])

// ]);
