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

class StudentsResource extends Resource
{
    protected static ?string $model = Students::class;
    protected static ?string $navigationGroup = 'Student Information';

    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationIcon = 'heroicon-s-user';

    public static function form(Form $form): Form
    {
        return $form
            // main student information section - table: students
            ->schema([
                Section::make('General Student Information')
                    ->description("Enter the student's general information.")
                    ->schema([
                        TextInput::make('last_name')
                            ->label("Last Name"),
                        TextInput::make('first_name')
                            ->label("First Name"),
                        TextInput::make('middle_name')
                            ->label("Middle Name"),
                        TextInput::make('suffix')
                            ->label("Suffix"),
                        Select::make('sex')
                            ->label('Sex')
                            ->options([
                                'M' => 'Male',
                                'F' => 'Female',
                            ])
                            ->required(),
                        TextInput::make('address')
                            ->label("Address")
                            ->required(),
                        DatePicker::make('birthdate')
                            ->label("Date of Birth")
                            ->required(),
                        TextInput::make('birthplace')
                            ->label('Place of Birth')
                            ->required(),
                        TextInput::make('gwa')
                            ->label('General Weighted Average')
                            ->required(),
                        TextInput::make('nstp_number')
                            ->label('NSTP Number')
                            ->required(),
                    ]),
                // student's graduation information section - table: students_graduation_infos
                Section::make('Student Graduation Information')
                    ->relationship('graduationInfos')
                    ->description("Enter the student's graduation information.")
                    ->schema([
                        DatePicker::make('graduation_date')
                            ->label('Date of Graduation')
                            ->required(),
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
                        TextInput::make('dates_of_attendance')
                            ->label('Dates of Attendance (Month Year - Month Year)')
                            ->required(),
                    ]),

                // student's registration information section - table: students_registration_infos
                Section::make('Student Registration Information')
                    ->description("Enter the student's registration information.")
                    ->relationship('registrationInfos')
                    ->schema([
                        TextInput::make('last_school_attended')
                            ->required()
                            ->label('Last School Attended (High School/College)'),
                        TextInput::make('last_year_attended')
                            ->label('Last Year Attended (Date graduated/last attended)')
                            ->required(),
                        TextInput::make('category')
                            ->label('Category')
                            ->required(),
                        Select::make('acad_year_id')
                            ->label('Select Academic Year')
                            ->required()
                            ->options(AcadYears::all()->pluck('year', 'id'))
                            ->searchable()
                            ->reactive()
                            ->getSearchResultsUsing(fn(string $query) => AcadYears::where('year', 'like', "%{$query}%")->get()->pluck('year', 'id'))
                            ->getOptionLabelUsing(fn($value) => AcadYears::find($value)?->year ?? 'Unknown Year'),
                        Select::make('acad_term_id')
                            ->label('Select Academic Term (Date/Semester admitted)')
                            ->required()
                            ->reactive()
                            ->options(function ($get) {
                                $acadYearId = $get('acad_year_id');
                                if ($acadYearId) {
                                    return AcadTerms::where('acad_year_id', $acadYearId)->pluck('acad_term', 'id');
                                }
                                return [];
                            })
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $query) => AcadTerms::where('acad_term', 'like', "%{$query}%")->get()->pluck('acad_term', 'id'))
                            ->getOptionLabelUsing(fn($value) => AcadTerms::find($value)?->acad_term ?? 'Unknown Academic Term')
                    ]),
                //student's grades - table: students_records
                Section::make('Student Records')
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
                            ->options(function ($get) {
                                $campus_id = $get('campus_id');
                                if ($campus_id) {
                                    return Colleges::where('campus_id', $campus_id)->pluck('college_name', 'id');
                                }
                                return [];
                            })
                            ->searchable(),
                        Select::make('program_id')
                            ->label('Select Program')
                            ->required()
                            ->reactive()
                            ->options(function ($get) {
                                $college_id = $get('college_id');
                                if ($college_id) {
                                    return Programs::where('college_id', $college_id)->pluck('program_name', 'id');
                                }
                                return [];
                            })
                            ->searchable()
                            ->getOptionLabelUsing(fn($value) => Programs::find($value)?->program_name ?? 'Unknown Program'),
                        Select::make('program_major_id')
                            ->label('Select Program Major')
                            ->reactive()
                            ->options(function ($get) {
                                $program_id = $get('program_id');
                                if ($program_id) {
                                    return ProgramsMajor::where('program_id', $program_id)->pluck('program_major_name', 'id');
                                }
                                return [];
                            })
                            ->searchable()
                            ->getOptionLabelUsing(fn($value) => ProgramsMajor::find($value)?->program_major_name ?? 'Unknown Major'),
                        //Conditional logic based on the Toggle State
                        Toggle::make('is_regular')
                            ->label('Regular Student')
                            ->default(true)
                            ->reactive(),
                        Repeater::make('records')
                            ->label('Grades')
                            // ->relationship('records')
                            ->reactive()
                            ->visible(fn($get) => $get('is_regular'))
                            ->schema([
                                Select::make('curricula_id')
                                    ->label('Select Curriculum')
                                    ->required()
                                    ->reactive() // Make the Select reactive
                                    ->options(function ($get) {
                                        // Fetch the outer selections
                                        $program_id = $get('../../program_id');
                                        $program_major_id = $get('../../program_major_id');
                                        if ($program_id && $program_major_id) {
                                            return Curricula::where('program_id', $program_id)
                                                ->where('program_major_id', $program_major_id)
                                                ->pluck('curricula_name', 'id');
                                        } elseif ($program_id) {
                                            return Curricula::where('program_id', $program_id)
                                                ->pluck('curricula_name', 'id');
                                        }
                                        return [];
                                    })
                                    ->searchable()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        // Update records automatically if a curriculum is selected
                                        if ($state) {
                                            $courses = Courses::where('curricula_id', $state)->get();
                                            $set('records', $courses->map(function ($course) {
                                                return [
                                                    'course_id' => $course->id,
                                                    'descriptive_title' => $course->descriptive_title,
                                                    'course_unit' => $course->course_unit,
                                                ];
                                            })->toArray());
                                        }
                                    }),
                                Repeater::make('courses')
                                    ->label('Courses Taken by Student')
                                    ->reactive()
                                    ->schema([
                                        Select::make('course_id')
                                            ->label('Course Code')
                                            ->options(function ($get) {
                                                $curricula_id = $get('../../curricula_id');
                                                if ($curricula_id) {
                                                    return Courses::where('curricula_id', $curricula_id)->pluck('course_code', 'id');
                                                }
                                                return [];
                                            })
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                $course = Courses::find($state);
                                                $set('descriptive_title', $course ? $course->descriptive_title : 'Unknown Descriptive Title');
                                                $set('course_unit', $course ? $course->course_unit : 'Unknown Units');
                                            })
                                            ->searchable()
                                            ->getOptionLabelUsing(fn($value) => Courses::find($value)?->course_code ?? 'Unknown Course Code'),
                                        TextInput::make('descriptive_title')
                                            ->label('Descriptive Title')
                                            ->disabled(),
                                        Toggle::make('incomplete')
                                            ->label('Incomplete')
                                            ->default(false)
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $set('final_grade', 'INC');
                                                }
                                            }),
                                        TextInput::make('final_grade')
                                            ->live()
                                            ->label('Final Grade')
                                            ->readonly(fn($get) => $get('incomplete'))
                                            ->required(),
                                        TextInput::make('removal_rating')
                                            ->label('Removal Rating'),
                                        TextInput::make('course_unit')
                                            ->label('Units of Credit')
                                            ->disabled(),
                                    ])
                                    ->columnSpan('full'),
                            ]),
                    ]),
            ]);
    }
    public static function table(Table $table): Table
    {
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
