<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentsResource\Pages;
use App\Filament\Resources\StudentsResource\RelationManagers;
use App\Models\Students;
use App\Models\AcadTerms;
use App\Models\AcadYears;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
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
use Filament\Infolists\Components\Tabs;
use Illuminate\Validation\Rule;
use Filament\Notifications\Notification;

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
                        TextInput::make('last_name')->label("Last Name"),
                        TextInput::make('first_name')->label("First Name"),
                        TextInput::make('middle_name')->label("Middle Name"),
                        TextInput::make('suffix')->label("Suffix"),
                        Select::make('sex')->label('Sex')->options([
                            'M' => 'Male',
                            'F' => 'Female',
                        ])->required(),
                        TextInput::make('address')->label("Address")->required(),
                        Select::make('province')
                            ->label("Province")
                            ->options([
                                'albay' => 'Albay',
                                'camarines_norte' => 'Camarines Norte',
                                'camarines_sur' => 'Camarines Sur',
                                'catanduanes' => 'Catanduanes',
                                'masbate' => 'Masbate',
                                'sorsogon' => 'Sorsogon',
                            ])
                            ->required()
                            ->reactive(),

                            Select::make('city')
                            ->label("City")
                            ->options(function (callable $get) {
                                $province = $get('province');
                        
                                $cities = [
                                    'albay' => [
                                        'legazpi' => 'Legazpi',
                                        'ligao' => 'Ligao',
                                        'tabaco' => 'Tabaco',
                                    ],
                                    'camarines_norte' => [
                                        'daet' => 'Daet',
                                        'vinzons' => 'Vinzons',
                                        'labo' => 'Labo',
                                    ],
                                    'camarines_sur' => [
                                        'naga' => 'Naga',
                                        'iriga' => 'Iriga',
                                        'buhi' => 'Buhi',
                                    ],
                                    'catanduanes' => [
                                        'virac' => 'Virac',
                                        'san_andres' => 'San Andres',
                                        'bagamanoc' => 'Bagamanoc',
                                    ],
                                    'masbate' => [
                                        'masbate_city' => 'Masbate City',
                                        'aroroy' => 'Aroroy',
                                        'baleno' => 'Baleno',
                                    ],
                                    'sorsogon' => [
                                        'sorsogon_city' => 'Sorsogon City',
                                        'castilla' => 'Castilla',
                                        'gubat' => 'Gubat',
                                    ],
                                ];
                        
                                return $cities[$province] ?? [];
                            })
                            ->required(),


                        DatePicker::make('birthdate')->label("Date of Birth")->required(),
                        TextInput::make('birthplace')->label('Place of Birth')->required(),
                        TextInput::make('gwa')->label('General Weighted Average')->required(),
                        TextInput::make('nstp_number')->label('NSTP Number')->required(),
                        ]),

// Student's graduation information section - table: students_graduation_infos
Section::make('Student Graduation Information')
    ->relationship('graduationInfos')
    ->description("Enter the student's graduation information.")
    ->schema([
        DatePicker::make('graduation_date')
            ->label('Date of Graduation')
            ->required()
            ->reactive(),
        TextInput::make('board_approval')
            ->label('Special Order Number (Board Resolution)')
            ->required(),
        Select::make('latin_honor')
            ->label('Latin Honor')
            ->options([
                'Cum Laude' => 'Cum Laude',
                'Magna Cum Laude' => 'Magna Cum Laude',
                'Summa Cum Laude' => 'Summa Cum Laude',
                'Academic Distinction' => 'Academic Distinction',
                'With Honor' => 'With Honor',
                'With High Honor' => 'With High Honor',
                'With Highest Honor' => 'With Highest Honor',
            ]),
        Select::make('degree_attained')
            ->label('Degree Attained')
            ->options([
                "Bachelor's Degree" => "Bachelor's Degree",
                "Master's Degree" => "Master's Degree",
                'Doctorate Degree' => 'Doctorate Degree',
            ]),

            //Gumagana Minsan? Di ko knows why :(
            TextInput::make('dates_of_attendance')
            ->label('Dates of Attendance (Month Year - Month Year)')
            ->required()
            ->reactive()
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $dates = explode(' - ', $state);
                
                if (count($dates) == 2) {
                    try {
                        $startDate = \Carbon\Carbon::createFromFormat('F Y', trim($dates[0]))->startOfMonth();
                        $endDate = \Carbon\Carbon::createFromFormat('F Y', trim($dates[1]))->endOfMonth();
                        $graduationDate = $get('graduation_date');
        
                        if ($graduationDate) {
                            // Ensure the graduation date uses the same format
                            $graduationDate = \Carbon\Carbon::createFromFormat('Y-m-d', $graduationDate)->startOfMonth();
                            
                            // Clone the endDate before adding a year
                            $validEndDate = (clone $endDate)->addYear();
        
                            // Validate graduation date range
                            if ($graduationDate->lt($startDate) || $graduationDate->gt($validEndDate)) {
                                Notification::make()
                                    ->title('Validation Error')
                                    ->body('The graduation date must be within the dates of attendance or up to 1 year past the end date.')
                                    ->danger()
                                    ->send();
                            }
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Invalid Date Format')
                            ->body('Please enter the correct date format: "Month Year - Month Year" (e.g., June 2019 - May 2023).')
                            ->danger()
                            ->send();
                    }
                }
            })
    ]),

                // Student's registration information section - table: students_registration_infos
                Section::make('Student Registration Information')
                ->relationship('registrationInfos')
                ->description("Enter the student's registration information.")
                ->schema([
                    TextInput::make('last_school_attended')
                        ->required()
                        ->label('Last School Attended (High School/College)'),
            
                    TextInput::make('last_year_attended')
                        ->label('Last Year Attended (Date graduated/last attended)')
                        ->required()
                        ->maxLength(4)
                        ->numeric(),
            
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
                        ->getOptionLabelUsing(fn($value) => AcadTerms::find($value)?->acad_term ?? 'Unknown Academic Term')
                        ]), 
            

Toggle::make('is_regular')
->label('Regular Student')
->default(true)
->reactive(),

// Student's grades - table: students_records
Section::make('Student Records (Regular)')
->visible(fn($get) => $get('is_regular'))
->schema([
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
        ->options(
            function ($get) {
                $campus_id = $get('campus_id');
                if ($campus_id) {
                    return Colleges::where('campus_id', $campus_id)->pluck('college_name', 'id');
                }
                return [];
            }
        )
        ->searchable(),

    Select::make('program_id')
        ->label('Select Program')
        ->required()
        ->reactive()
        ->options(
            function ($get) {
                $college_id = $get('college_id');
                if ($college_id) {
                    return Programs::where('college_id', $college_id)->pluck('program_name', 'id');
                }
                return [];
            }
        )
        ->searchable()
        ->getOptionLabelUsing(fn($value) => Programs::find($value)?->program_name ?? 'Unknown Program'),

    Select::make('program_major_id')
        ->label('Select Program Major')
        ->reactive()
        ->options(
            function ($get) {
                $program_id = $get('program_id');
                if ($program_id) {
                    return ProgramsMajor::where('program_id', $program_id)->pluck('program_major_name', 'id');
                }
                return [];
            }
        )
        ->searchable()
        ->getOptionLabelUsing(fn($value) => ProgramsMajor::find($value)?->program_major_name ?? 'Unknown Major'),

    Repeater::make('records_regular')
        ->label('Grades')
        ->relationship('records')
        ->reactive()
        ->schema([
            Select::make('curricula_id')
                ->label('Select Curriculum')
                ->required()
                ->reactive()
                ->options(
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
                )
                ->searchable()
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

            Repeater::make('records_regular_grades')
                ->label('Courses & Grades')
                ->reactive()
                ->schema([
                    Select::make('course_id')
                        ->label('Course Code')
                        ->options(
                            function ($get) {
                                $curricula_id = $get('curricula_id');
                                if ($curricula_id) {
                                    return Courses::where('curricula_id', $curricula_id)->pluck('course_code', 'id');
                                }
                                return [];
                            }
                        )
                        ->reactive()
                        ->searchable()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $course = Courses::find($state);
                            $set('descriptive_title', $course ? $course->descriptive_title : 'Unknown Descriptive Title');
                            $set('course_unit', $course ? $course->course_unit : 'Unknown Units');
                        }),

                    TextInput::make('descriptive_title')
                        ->label('Descriptive Title')
                        ->disabled(),

                    TextInput::make('final_grade')
                        ->label('Final Grade')
                        ->required(),

                    TextInput::make('removal_rating')
                        ->label('Removal Rating'),

                    TextInput::make('course_unit')
                        ->label('Units of Credit')
                        ->disabled(),
                ]),
        ])
]),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                /*TextColumn::make('last_name')
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
                    ->sortable(),*/
                /*TextColumn::make('name')
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
                    }),*/
                    ////////
                    // TextColumn::make('name')
                    // ->label('Name')
                    // ->getStateUsing(fn ($record) => 
                    //     "{$record->first_name}" . 
                    //     ($record->middle_name ? " {$record->middle_name}" : "") . 
                    //     " {$record->last_name}" . 
                    //     ($record->suffix ? " {$record->suffix}" : "")
                    // )
                    // ->sortable(query: function ($query, $direction) {
                    //     $query->orderBy('first_name', $direction)
                    //           ->orderBy('middle_name', $direction)
                    //           ->orderBy('last_name', $direction);
                    // })
                    // ->searchable(query: function ($query, $search) {
                    //     $query->where(function ($q) use ($search) {
                    //         $q->where('first_name', 'like', "%{$search}%")
                    //           ->orWhere('middle_name', 'like', "%{$search}%")
                    //           ->orWhere('last_name', 'like', "%{$search}%")
                    //           ->orWhere('suffix', 'like', "%{$search}%");
                    //     });
                    // }),
                    // TextColumn::make('course')
                    // ->label('Program')
                    // ->getStateUsing(function ($record) {
                    //     if ($record->curriculum_id) {
                    //         $curricula = Curricula::find($record->curriculum_id);
                    //         if ($curricula && str_contains($curricula->curricula_name, ',')) {
                    //             return trim(explode(',', $curricula->curricula_name)[1]);
                    //         }
                    //         return $curricula ? $curricula->curricula_name : 'N/A';
                    //     }
                    //     return 'N/A';
                    // })
                    // /*->sortable(query: function ($query, $direction) {
                    //     $query->leftJoin('curriculas', 'students.curriculum_id', '=', 'curriculas.id')
                    //           ->orderBy('curriculas.curricula_name', $direction);
                    // })*/
                    // ->searchable(query: function ($query, $search) {
                    //     $query->leftJoin('curriculas', 'students.curriculum_id', '=', 'curriculas.id')
                    //           ->where('curriculas.curricula_name', 'like', "%{$search}%");
                    // }),
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
    ->label('Course')
    ->getStateUsing(function ($record) {
        if ($record->curriculum_id) {
            $curricula = Curricula::find($record->curriculum_id);
            if ($curricula && str_contains($curricula->curricula_name, ',')) {
                return trim(explode(',', $curricula->curricula_name)[1]);
            }
            return $curricula ? $curricula->curricula_name : 'N/A';
        }
        return 'N/A';
    })
    ->sortable(query: function ($query, $direction) {
        $query->leftJoin('curriculas', 'students.curriculum_id', '=', 'curriculas.id')
              ->orderBy('curriculas.curricula_name', $direction);
    })
    ->searchable(query: function ($query, $search) {
        $query->leftJoin('curriculas', 'students.curriculum_id', '=', 'curriculas.id')
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
    //                 TextColumn::make('name')
    //                 ->label('Name')
    //                 ->getStateUsing(fn ($record) => 
    //                     "{$record->first_name}" . 
    //                     ($record->middle_name ? " {$record->middle_name}" : "") . 
    //                     " {$record->last_name}" . 
    //                     ($record->suffix ? " {$record->suffix}" : "")
    //                 )
    //                 ->sortable(query: function ($query, $direction) {
    //                     $query->orderBy('first_name', $direction)
    //                           ->orderBy('middle_name', $direction)
    //                           ->orderBy('last_name', $direction);
    //                 })
    //                 ->searchable(query: function ($query, $search) {
    //                     $query->where(function ($q) use ($search) {
    //                         $q->where('first_name', 'like', "%{$search}%")
    //                           ->orWhere('middle_name', 'like', "%{$search}%")
    //                           ->orWhere('last_name', 'like', "%{$search}%")
    //                           ->orWhere('suffix', 'like', "%{$search}%");
    //                     });
    //                 }),

    // //                 TextColumn::make('course')    WORKING SEARCH AND SORT BUT COURSE ONLY
    // // ->label('Program')
    // // ->getStateUsing(function ($record) {
    // //     if ($record->curriculum_id) {
    // //         $curricula = Curricula::find($record->curriculum_id);
    // //         if ($curricula && str_contains($curricula->curricula_name, ',')) {
    // //             return trim(explode(',', $curricula->curricula_name)[1]);
    // //         }
    // //         return $curricula ? $curricula->curricula_name : 'N/A';
    // //     }
    // //     return 'N/A';
    // // })
    // // ->sortable(query: function ($query, $direction) {
    // //     $query->select('students.*', 'curriculas.curricula_name') // Ensure students data is not lost
    // //           ->leftJoin('curriculas', 'students.curriculum_id', '=', 'curriculas.id')
    // //           ->orderBy('curriculas.curricula_name', $direction);
    // // })
    // // ->searchable(query: function ($query, $search) {
    // //     $query->leftJoin('curriculas', 'students.curriculum_id', '=', 'curriculas.id')
    // //           ->where('curriculas.curricula_name', 'like', "%{$search}%");
    // // }),

    // TextColumn::make('graduation_date')
    //                 ->label('Date of Graduation')
    //                 ->getStateUsing(function ($record) {
    //                     $graduationInfo = $record->graduationInfos()->first();
    //                     if ($graduationInfo && $graduationInfo->graduation_date) {
    //                         return date('F d, Y', strtotime($graduationInfo->graduation_date));
    //                     }
    //                     return 'N/A';
    //                 })
    //                 ->sortable(query: function ($query, $direction) {
    //                     $query->leftJoin('students_graduation_infos', 'students.id', '=', 'students_graduation_infos.student_id')
    //                         ->orderBy('students_graduation_infos.graduation_date', $direction);
    //                 })
    //                 ->searchable(query: function ($query, $search) {
    //                     $query->leftJoin('students_graduation_infos', 'students.id', '=', 'students_graduation_infos.student_id')
    //                         ->where('students_graduation_infos.graduation_date', 'like', "%{$search}%");
    //                 })
                    // Added closing bracket for columns array
                    // TextColumn::make('course')
                    // ->label('Program')
                    // ->getStateUsing(function ($record) {
                    //     if ($record->curriculum_id) {
                    //         $curricula = Curricula::find($record->curriculum_id);
                    //         if ($curricula && str_contains($curricula->curricula_name, ',')) {
                    //             return trim(explode(',', $curricula->curricula_name)[1]);
                    //         }
                    //         return $curricula ? $curricula->curricula_name : 'N/A';
                    //     }
                    //     return 'N/A';
                    // })
                    // /*->sortable(query: function ($query, $direction) {
                    //     $query->leftJoin('curriculas', 'students.curriculum_id', '=', 'curriculas.id')
                    //           ->orderBy('curriculas.curricula_name', $direction);
                    // })*/
                    // ->searchable(query: function ($query, $search) {
                    //     $query->leftJoin('curriculas', 'students.curriculum_id', '=', 'curriculas.id')
                    //           ->where('curriculas.curricula_name', 'like', "%{$search}%");
                    // }),
                    

                /*TextColumn::make('sex')
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
                    ->sortable(),*/
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
