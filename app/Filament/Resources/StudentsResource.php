<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentsResource\Pages;
use App\Filament\Resources\StudentsResource\RelationManagers;
use App\Models\Students;
use App\Models\AcadTerms;
use App\Models\AcadYears;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
<<<<<<< Updated upstream
=======
use Filament\Forms\Components\Grid;
>>>>>>> Stashed changes
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
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
<<<<<<< Updated upstream
=======
use Filament\Forms\Components\Hidden;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
>>>>>>> Stashed changes
use Filament\Infolists\Components\Tabs;

class StudentsResource extends Resource
{
    protected static ?string $model = Students::class;
    protected static ?string $navigationGroup = 'Student Information';

    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            // Main student information section - table: students
            ->schema([
                Section::make('General Student Information')
                    ->description("Enter the student's general information.")
                    ->schema([
<<<<<<< Updated upstream
=======
                        Grid::make(2)->schema([
>>>>>>> Stashed changes
                        TextInput::make('last_name')->label("Last Name"),
                        TextInput::make('first_name')->label("First Name"),
                        TextInput::make('middle_name')->label("Middle Name"),
                        TextInput::make('suffix')->label("Suffix"),
<<<<<<< Updated upstream
=======
                        ]),
                        Grid::make(2)->schema([
>>>>>>> Stashed changes
                        Select::make('sex')->label('Sex')->options([
                            'M' => 'Male',
                            'F' => 'Female',
                        ])->required(),
<<<<<<< Updated upstream
                        TextInput::make('address')->label("Address")->required(),
                        DatePicker::make('birthdate')->label("Date of Birth")->required(),
                        TextInput::make('birthplace')->label('Place of Birth')->required(),
=======
                        DatePicker::make('birthdate')->label("Date of Birth")->required(),
                        ]),
                        TextInput::make('address')->label("Address")->required(),
                        // DatePicker::make('birthdate')->label("Date of Birth")->required(),
                        TextInput::make('birthplace')->label('Place of Birth')->required(),

                        Grid::make(2)->schema([
>>>>>>> Stashed changes
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
                        ->required(fn ($get) => $get('category') === 'Others')
                        ->visible(fn ($get) => $get('category') === 'Others'),
                        ]),
                        Grid::make(2)->schema([
                    Select::make('acad_year_id')
                        ->label('Select Academic Year')
                        ->required()
                        ->options(AcadYears::all()->pluck('year', 'id'))
                        ->searchable()
                        ->reactive()
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

                Toggle::make('is_regular')->label('Regular Student')->default(true)->reactive(),
                // Student's grades - table: students_records
                Section::make('Student Records (Regular)')
                    ->visible(fn($get) => $get('is_regular'))
                    ->schema([
                        Grid::make(2)->schema([
                        Select::make('campus_id')->label('Select Campus')->required()->reactive()
                        ->options( Campuses::all()->pluck('campus_name', 'id')
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
<<<<<<< Updated upstream

                        Select::make('program_id')->label('Select Program')->required()->reactive()->options(
                            function ($get) {
                                $college_id = $get('college_id');
                                if ($college_id) {
                                    return Programs::where('college_id', $college_id)->pluck('program_name', 'id');
                                }
                                return [];
                            }
                        )->searchable()->getOptionLabelUsing(fn($value) => Programs::find($value)?->program_name ?? 'Unknown Program'),

=======
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

>>>>>>> Stashed changes
                        Select::make('program_major_id')->label('Select Program Major')->reactive()->options(
                            function ($get) {
                                $program_id = $get('program_id');
                                if ($program_id) {
                                    return ProgramsMajor::where('program_id', $program_id)->pluck('program_major_name', 'id');
                                }
                                return [];
                            }
                        )->searchable()->getOptionLabelUsing(fn($value) => ProgramsMajor::find($value)?->program_major_name ?? 'Unknown Major'),
<<<<<<< Updated upstream



                        Repeater::make('records_regular')->label('Grades')->reactive()
=======
                        ]),



                        Repeater::make('records_regular')->label('Curriculum')
>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
                                Repeater::make('records_regular_grades')
                                    ->label('Courses & Grades')
                                    ->reactive()
                                    ->schema([
                                        Select::make('course_code')->label('Course Code')->options(
=======
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
>>>>>>> Stashed changes
                                            function ($get) {
                                                $curricula_id = $get('../../curricula_id'); // Ensure correct path
                                                if ($curricula_id) {
                                                    return Courses::where('curricula_id', $curricula_id)
                                                        ->pluck('course_code', 'id'); // Fetch courses based on curriculum
                                                }
                                                return [];
                                            }
<<<<<<< Updated upstream
                                        )->reactive()->searchable()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                $course = Courses::find($state);
                                                $set('descriptive_title', $course ? $course->descriptive_title : 'Unknown Descriptive Title');
                                                $set('course_unit', $course ? $course->course_unit : 'Unknown Units');
                                            }),
                                        TextInput::make('descriptive_title')->label('Descriptive Title')->disabled(),
                                        TextInput::make('final_grade')->label('Final Grade')->required(),
=======
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
>>>>>>> Stashed changes
                                        TextInput::make('removal_rating')->label('Removal Rating'),
                                        TextInput::make('course_unit')->label('Units of Credit')->disabled(),
                                    ]),
                            ])
<<<<<<< Updated upstream

                    ]),
                Section::make('Student Records (Irregular)')
                    ->visible(fn($get) => !$get('is_regular'))
                    ->schema([
                        Repeater::make('records_irregular')
                            ->reactive()
                            ->schema([
                                Select::make('campus_id')
                                    ->label('Select Campus')
                                    ->required()
                                    ->reactive()
                                    ->options(
                                        Campuses::all()->pluck('campus_name', 'id')
                                    ),
                                Select::make('college_id')
                                    ->label('Select College')
                                    ->required()
                                    ->reactive()
                                    ->options(
                                        function ($get) {
                                            $campus_id = $get('campus_id');
                                            if ($campus_id) {
                                                return Colleges::where('campus_id', $campus_id)->pluck('college_name', 'id');
                                            }
                                            return [];
                                        }
                                    )->searchable(),
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
                            ])

                    ])
=======

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
                                    Select::make('course_code')
                                        ->label('Course Code')
                                        ->required()
                                        ->options(function ($get) {
                                            return Courses::where('curricula_id', $get('../../curricula_id'))
                                                ->pluck('course_code', 'id') ?? [];
                                        })
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
                                        })
                                        ->disabled(fn($get) => $get('is_preloaded')),

                                    Hidden::make('is_preloaded'),

                                    TextInput::make('descriptive_title')
                                        ->label('Descriptive Title')
                                        ->disabled(fn($get) => $get('is_preloaded')),

                                    TextInput::make('final_grade')
                                        ->label('Final Grade')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('removal_rating')
                                        ->label('Removal Rating'),

                                    TextInput::make('course_unit')
                                        ->label('Units of Credit')
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
                ->collapsible(),
        ]),
>>>>>>> Stashed changes
            ]);
    }
    public static function table(Table $table): Table
    {
<<<<<<< Updated upstream
        return $table
            ->columns([
                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('middle_name')
                    ->label('Middle Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('suffix')
                    ->label('Suffix')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sex')
                    ->label('Sex')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address')
                    ->label('Address')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('birthdate')
                    ->label('Date of Birth')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('birthplace')
                    ->label('Place of Birth')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
=======
        $user = Filament::auth()->user();
        return $table
        ->query(
            Students::query()
                        ->when(
                            !$user->roles->contains('name', 'super_admin'), // If NOT super admin
                            fn ($query) => $query->whereNull('students.deleted_at') // Specify the table explicitly
                        )
                )
            ->columns([
                // TextColumn::make('last_name')
                //     ->label('Last Name')
                //     ->searchable()
                //     ->sortable(),
                // TextColumn::make('first_name')
                //     ->label('First Name')
                //     ->searchable()
                //     ->sortable(),
                // TextColumn::make('middle_name')
                //     ->label('Middle Name')
                //     ->searchable()
                //     ->sortable(),
                // TextColumn::make('suffix')
                //     ->label('Suffix')
                //     ->searchable()
                //     ->sortable(),
                // TextColumn::make('sex')
                //     ->label('Sex')
                //     ->searchable()
                //     ->sortable(),
                // TextColumn::make('address')
                //     ->label('Address')
                //     ->searchable()
                //     ->sortable(),
                // TextColumn::make('birthdate')
                //     ->label('Date of Birth')
                //     ->searchable()
                //     ->sortable(),
                // TextColumn::make('birthplace')
                //     ->label('Place of Birth')
                //     ->searchable()
                //     ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->getStateUsing(fn ($record) => 
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
                        $query->where(function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('suffix', 'like', "%{$search}%");
                        });
                    }),
                    TextColumn::make('course')
                    ->label('Program')
                    ->getStateUsing(function ($record) {
                        $studentRecord = $record->records()->first(); // Fetch related student record
                        if ($studentRecord && $studentRecord->curricula_id) {
                            $curricula = Curricula::find($studentRecord->curricula_id);
                            if ($curricula) {
                                $segments = explode(',', $curricula->curricula_name);
                                return isset($segments[1]) ? trim($segments[1]) : trim($segments[0]);
                            }
                        }
                        return 'N/A';
                    })
                    ->sortable(query: function ($query, $direction) {
                        $query->leftJoin('students_records', 'students.id', '=', 'students_records.student_id') // First join with students_records
                            ->leftJoin('curriculas', 'students_records.curriculum_id', '=', 'curriculas.id') // Then join with curriculas
                            ->orderBy('curriculas.curricula_name', $direction);
                    })
                    ->searchable(query: function ($query, $search) {
                        $query->leftJoin('students_records', 'students.id', '=', 'students_records.student_id')
                            ->leftJoin('curriculas', 'students_records.curriculum_id', '=', 'curriculas.id')
                            ->whereNotNull('curriculas.curricula_name')
                            ->where('curriculas.curricula_name', 'like', "%{$search}%");
                    }),
                TextColumn::make('major')
                    ->label('Major')
                    ->getStateUsing(function ($record) {
                        if ($record->curriculum_id) {
                            $curricula = Curricula::find($record->curriculum_id);
                            if ($curricula && $curricula->program_major_id) {
                                $programMajor = ProgramsMajor::find($curricula->program_major_id);
                                return $programMajor ? $programMajor->program_major_name : 'N/A';
                            }
                        }
                        return 'N/A';
                    })
                    ->sortable(query: function ($query, $direction) {
                        $query->leftJoin('curriculas', 'students.curriculum_id', '=', 'curriculas.id')
                            ->leftJoin('programs_major', 'curriculas.program_major_id', '=', 'programs_major.id')
                            ->orderBy('programs_major.program_major_name', $direction);
                    })
                    ->searchable(query: function ($query, $search) {
                        $query->leftJoin('curriculas', 'students.curriculum_id', '=', 'curriculas.id')
                            ->leftJoin('programs_major', 'curriculas.program_major_id', '=', 'programs_major.id')
                            ->where('programs_major.program_major_name', 'like', "%{$search}%");
                    }),
                TextColumn::make('graduation_date')
                    ->label('Date of Graduation')
                    ->getStateUsing(function ($record) {
                        $graduationInfo = $record->graduationInfos()->first();
                        if ($graduationInfo && $graduationInfo->graduation_date) {
                            return date('F d, Y', strtotime($graduationInfo->graduation_date));
                        }
                        return 'N/A';
                    })
                    ->sortable(query: function ($query, $direction) {
                        $query->leftJoin('students_graduation_infos', 'students.id', '=', 'students_graduation_infos.student_id')
                            ->orderBy('students_graduation_infos.graduation_date', $direction);
                    })
                    ->searchable(query: function ($query, $search) {
                        $query->leftJoin('students_graduation_infos', 'students.id', '=', 'students_graduation_infos.student_id')
                            ->where('students_graduation_infos.graduation_date', 'like', "%{$search}%");
                    }),
                    TextColumn::make('status')
                    ->label('Status')
                    ->badge() // Turns text into a badge
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->color(fn (string $state): string => match ($state) {
                        'verified' => 'success',   // Green badge for verified
                        'unverified' => 'danger',  // Red badge for unverified
                        default => 'gray',         // Default gray badge
                }),
                ])   
                ->emptyStateIcon('heroicon-s-user')
                ->emptyStateHeading('Student Not Available')
                ->emptyStateDescription('There are currently no students in the system.')
                ->filters([
                    // Show the "Trashed" filter ONLY if the user is a super admin
                ...($user->roles->contains('name', 'super_admin') ? [Tables\Filters\TrashedFilter::make()] : [])
                ])
                ->actions([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    // Show "Restore" button only for super_admin
                ...($user->roles->contains('name', 'super_admin') ? [Tables\Actions\RestoreAction::make()] : [])
                ])
                ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]),
>>>>>>> Stashed changes
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
    
}
