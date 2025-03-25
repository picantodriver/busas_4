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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\FileUpload;
use Barryvdh\DomPDF\Facade\Pdf;
use function array_merge;
use Illuminate\Support\Str;
use Filament\Support\Enums\ActionSize;
use App\Traits\HasHashedRouteKey;


class StudentsResource extends Resource
{
    use HasHashedRouteKey;

    protected static ?string $model = Students::class;
    protected static ?string $navigationGroup = 'Student Information';

    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function getNavigationLabel(): string
    {
        return 'Students Records';
    }

    public static function form(Form $form): Form
    {
        return $form
            // Main student information section - table: students
            ->schema([
                Hidden::make('id')
                    ->disabled(false) // Allow the ID to be submitted
                    ->dehydrated(true) // Ensure the field is included in form submission
                    ->hiddenOn(['view', 'create']), // Hide on view and create pages 
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
                        // DatePicker::make('birthdate')->label("Date of Birth")->required(),

                        Grid::make(2)->schema([
                            TextInput::make('birthplace')->label('Place of Birth')->required(),

                            Select::make('country')
                        ->label('Country')
                        ->options(function () {
                            try {
                                // Try to read from local JSON file first
                                $jsonPath = storage_path('app/public/countries.json');

                                // Attempt to read local JSON file
                                if (file_exists($jsonPath)) {
                                    try {
                                        $countriesJson = file_get_contents($jsonPath);
                                        $countries = json_decode($countriesJson, true);

                                        // Validate JSON decoding
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($countries) && !empty($countries)) {
                                            Log::info('Using local countries JSON');
                                            return collect($countries)
                                                ->mapWithKeys(function ($country) {
                                                    return [$country['name'] => $country['name']];
                                                })
                                                ->sort()
                                                ->toArray();
                                        }
                                    } catch (\Exception $e) {
                                        Log::warning('Error reading local countries JSON: ' . $e->getMessage());
                                    }
                                }

                                // Fallback to API if local file fails
                                Log::info('Falling back to REST Countries API');
                                $response = Http::withOptions([
                                    'verify' => false,
                                    'timeout' => 0,    
                                ])->get('https://restcountries.com/v3.1/all');

                                if ($response->successful()) {
                                    return collect($response->json())
                                        ->pluck('name.common', 'name.common')
                                        ->sort()
                                        ->toArray();
                                }

                                // If API also fails, return an empty array
                                Log::error('Both local JSON and API fetch failed');
                                return [];
                            } catch (\Exception $e) {
                                Log::error('Unexpected error in country selection: ' . $e->getMessage());
                                return [];
                            }
                        })
                        ->searchable()
                        ->preload()
                        ->reactive()
                ]),

                Grid::make(3)->schema([
                    Select::make('region')
                        ->label("Region")
                        ->options(function () {
                            $response = Http::withOptions(['verify' => false])->get('https://raw.githubusercontent.com/isaacdarcilla/philippine-addresses/master/region.json');

                            // If API request fails, use local file as fallback
                            if (!$response->successful()) {
                                return collect(json_decode(Storage::get('regions.json'), true) ?? [])
                                    ->pluck('region_name', 'region_code');
                            }

                            $regions = $response->json();
                            if (!is_array($regions) || empty($regions)) {
                                return collect(json_decode(Storage::get('regions.json'), true) ?? [])
                                    ->pluck('region_name', 'region_code');
                            }

                            return collect($regions)->pluck('region_name', 'region_code');
                        })
                        ->afterStateUpdated(function (callable $set, $state) {
                            // Try API first
                            $response = Http::withOptions(['verify' => false])->get('https://raw.githubusercontent.com/isaacdarcilla/philippine-addresses/master/region.json');
                            $regions = [];

                            if ($response->successful()) {
                                $regions = $response->json();
                            } else {
                                // Fallback to local file
                                $regions = json_decode(Storage::get('regions.json'), true) ?? [];
                            }

                            $region = collect($regions)->firstWhere('region_code', $state);
                            if ($region) {
                                $set('region_name', $region['region_name']);
                            }

                            // Reset dependent fields
                            $set('province', null);
                            $set('province_name', null);
                            $set('city_municipality', null);
                            $set('city_municipality_name', null);
                        })
                        ->required()
                        ->reactive(),

                    Hidden::make('region_name'), // This will store the actual name

                    Select::make('province')
                        ->label("Province")
                        ->options(function (callable $get) {
                            $regionCode = $get('region');
                            if (!$regionCode) return [];

                            $response = Http::withOptions(['verify' => false])->get('https://raw.githubusercontent.com/isaacdarcilla/philippine-addresses/master/province.json');
                            $provinces = [];

                            if (!$response->successful()) {
                                // Fallback to local file
                                $provinces = json_decode(Storage::get('provinces.json'), true) ?? [];
                            } else {
                                $provinces = $response->json();
                            }

                            $filteredProvinces = collect($provinces)->filter(function ($province) use ($regionCode) {
                                return $province['region_code'] === $regionCode;
                            });

                            return $filteredProvinces->pluck('province_name', 'province_code');
                        })
                        ->afterStateUpdated(function (callable $set, $state) {
                            // Try API first
                            $response = Http::withOptions(['verify' => false])->get('https://raw.githubusercontent.com/isaacdarcilla/philippine-addresses/master/province.json');
                            $provinces = [];

                            if ($response->successful()) {
                                $provinces = $response->json();
                            } else {
                                // Fallback to local file
                                $provinces = json_decode(Storage::get('provinces.json'), true) ?? [];
                            }

                            $province = collect($provinces)->firstWhere('province_code', $state);
                            if ($province) {
                                $set('province_name', $province['province_name']);
                            }

                            // Reset dependent field
                            $set('city_municipality', null);
                            $set('city_municipality_name', null);
                        })
                        ->required()
                        ->reactive(),

                    Hidden::make('province_name'), // This will store the actual name

                    Select::make('city_municipality')
                        ->label("City/Municipality")
                        ->options(function (callable $get) {
                            $provinceCode = $get('province');
                            if (!$provinceCode) return [];

                            $response = Http::withOptions(['verify' => false])->get('https://raw.githubusercontent.com/isaacdarcilla/philippine-addresses/master/city.json');
                            $cities = [];

                            if (!$response->successful()) {
                                // Fallback to local file
                                $cities = json_decode(Storage::get('cities.json'), true) ?? [];
                            } else {
                                $cities = $response->json();
                            }

                            if (!is_array($cities) || empty($cities)) return [];

                            $filteredCities = collect($cities)->filter(function ($city) use ($provinceCode) {
                                return $city['province_code'] === $provinceCode;
                            });

                            return $filteredCities->pluck('city_name', 'city_code');
                        })
                        ->afterStateUpdated(function (callable $set, $state) {
                            // Try API first
                            $response = Http::withOptions(['verify' => false])->get('https://raw.githubusercontent.com/isaacdarcilla/philippine-addresses/master/city.json');
                            $cities = [];

                            if ($response->successful()) {
                                $cities = $response->json();
                            } else {
                                // Fallback to local file
                                $cities = json_decode(Storage::get('cities.json'), true) ?? [];
                            }

                            $city = collect($cities)->firstWhere('city_code', $state);
                            if ($city) {
                                $set('city_municipality_name', $city['city_name']);
                            }
                        })
                        ->required(),

                        Hidden::make('city_municipality_name'), // This will store the actual name
                        ]),

                        // Grid::make(2)->schema([
                        //     TextInput::make('gwa')->label('General Weighted Average')->required(),
                        //     TextInput::make('nstp_number')->label('NSTP Number')->required(),
                        // ]),

                        Grid::make(3)->schema([
                            TextInput::make('gwa')
                                ->label('General Weighted Average')
                                ->required()
                                ->rules([
                                    'numeric',
                                    'min:1',
                                    'max:5',
                                    'regex:/^\d*\.?\d{0,4}$/' // Allows decimal numbers with up to 4 decimal places
                                ])
                                ->inputMode('decimal')
                                ->step('0.01')
                                ->live()
                                ->debounce(500)
                                ->validationMessages([
                                    'numeric' => 'GWA must be a number',
                                    'min' => 'GWA must be at least 1.00',
                                    'max' => 'GWA must not exceed 5.00',
                                    'regex' => 'GWA must have up to 4 decimal places only'
                                ])
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if (!is_numeric($state)) {
                                        $set('gwa', null);
                                        Notification::make()
                                            ->title('Invalid Input')
                                            ->body('Please enter a valid number for GWA')
                                            ->danger()
                                            ->send();
                                    }
                                }),
                            TextInput::make('nstp_number')->label('NSTP Number')->required(),

                            Select::make('student_type')
                                ->label('Student Type')
                                ->options([
                                    'Undergraduate' => 'Undergraduate',
                                    'Graduate' => 'Graduate',
                                ])
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function (callable $set, $state) {
                                    $set('show_graduation_info', $state === 'Graduate');
                                }),
                        ]),
                    ]),

                // Student's graduation information section - table: students_graduation_infos
                Section::make('Student Graduation Information')
                    ->description("Enter the student's graduation information.")
                    ->visible(fn($get) => $get('show_graduation_info'))
                    ->schema([
                        Repeater::make('graduationInfos')
                            ->relationship('graduationInfos')
                            ->maxItems(1)
                            ->schema([
                                Grid::make(3)->schema([
                                    DatePicker::make('graduation_date')->label('Date of Graduation')->required(),
                                    TextInput::make('board_approval')->label('Special Order Number (Board Resolution)')->required(),
                                    Select::make('latin_honor')->label('Latin Honor')->options([
                                        'Cum Laude' => 'Cum Laude',
                                        'Magna Cum Laude' => 'Magna Cum Laude',
                                        'Summa Cum Laude' => 'Summa Cum Laude',
                                        'Academic Distinction' => 'Academic Distinction',
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
                            ])
                    ]),

                // Student's registration information section - table: students_registration_infos
                Section::make('Student Registration Information')
                    ->description("Enter the student's registration information.")
                    ->schema([
                        Repeater::make('registrationInfos')
                            ->relationship('registrationInfos')
                            ->maxItems(1)
                            ->schema([
                                TextInput::make('last_school_attended')
                                    ->required()
                                    ->label('Last School Attended (High School/College)'),
                                Grid::make(2)->schema([
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
                                        ->hidden(fn($livewire) => $livewire instanceof Pages\EditStudents || $livewire instanceof Pages\ViewStudents)
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

                    ]),
                Toggle::make('is_regular')->label('Regular Student')->default(true)->reactive()->hidden(fn($livewire) => $livewire instanceof EditStudents || $livewire instanceof Pages\ViewStudents),
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
                            ->label('Upload Document')
                            ->maxSize(10240)
                            ->acceptedFileTypes(['application/pdf'])
                            ->helperText('Maximum file size: 10MB. Accepted file types: PDF and images. File attachement is applicable only for Transferees.')
                            ->columnSpanFull()
                            ->hidden(fn($livewire) => $livewire instanceof Pages\ViewStudents),

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
                                            // ->afterStateUpdated(function ($state, callable $set) {
                                            $set('college_id', '');
                                            $set('program_id', '');
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
                                                    ->label('Course Code'),
                                                // ->required(),

                                                Hidden::make('is_preloaded'),

                                                TextInput::make('descriptive_title')
                                                    ->label('Descriptive Title')
                                                    ->required(),

                                                TextInput::make('final_grade')
                                                    ->label('Final Grade')
                                                    ->required()
                                                    ->maxLength(255),

                                                TextInput::make('removal_rating')
                                                    ->label('Removal Rating'),

                                                TextInput::make('course_unit')
                                                    ->label('Units of Credit')
                                                    ->required(),
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
                Section::make('Ladderized Graduation Information')
                    ->schema([
                        Repeater::make('ladderized')
                            ->schema([
                                Grid::make(1)
                                    ->schema([
                                        Select::make('acad_year_id')
                                            ->label('Select Academic Year')
                                            ->required() // Explicitly mark as required
                                            ->reactive()
                                            ->options(function () {
                                                return AcadYears::pluck('year', 'id');
                                            }),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('board_approval')
                                            ->label('Board Approval'),
                                        Select::make('latin_honor')
                                            ->label('Latin Honor')
                                            ->options([
                                                'With Honor' => 'With Honor',
                                                'With High Honor' => 'With High Honor',
                                                'With Highest Honor' => 'With Highest Honor',
                                            ]),
                                        TextInput::make('program_cert')
                                            ->label('Program Certificate'),
                                        DatePicker::make('graduation_date')
                                            ->label('Graduation Date'),
                                    ]),
                            ])
                            ->defaultItems(0) 
                            ->addActionLabel('Add Ladderized Program') 
                            ->collapsible() 
                            ->saveRelationshipsUsing(function ($record, array $state) {
                                foreach ($state as $itemData) {
                                    // Validate that acad_year_id exists before proceeding
                                    if (empty($itemData['acad_year_id'])) {
                                        throw new \Exception('Academic Year must be selected for ladderized record.');
                                    }
                                    
                                    // Check if a record with this acad_year_id already exists
                                    $existingRecord = $record->ladderized()
                                        ->where('acad_year_id', $itemData['acad_year_id'])
                                        ->first();
                                    
                                    if ($existingRecord) {
                                        // Update existing record
                                        $existingRecord->update([
                                            'board_approval' => $itemData['board_approval'] ?? null,
                                            'latin_honor' => $itemData['latin_honor'] ?? null,
                                            'program_cert' => $itemData['program_cert'] ?? null,
                                            'graduation_date' => $itemData['graduation_date'] ?? null,
                                        ]);
                                    } else {
                                        // Create new record
                                        $record->ladderized()->create([
                                            'acad_year_id' => $itemData['acad_year_id'],
                                            'board_approval' => $itemData['board_approval'] ?? null,
                                            'latin_honor' => $itemData['latin_honor'] ?? null,
                                            'program_cert' => $itemData['program_cert'] ?? null,
                                            'graduation_date' => $itemData['graduation_date'] ?? null,
                                        ]);
                                    }
                                }
                            })
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
                        !$user->roles->contains('name', 'Developer'), // If NOT super admin
                        fn($query) => $query->whereNull('students.deleted_at') // Specify the table explicitly
                    )
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->html()
                    ->getStateUsing(
                        fn($record) =>
                        "<strong>{$record->last_name},</strong>" .
                            ($record->middle_name ? " {$record->middle_name}" : "") .
                            " {$record->first_name}" .
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
                    })
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('Regular/Irregular')
                    ->label('Regular/Irregular')
                    ->getStateUsing(function ($record) {
                        return $record->is_regular ? 'Regular' : 'Irregular';
                    })
                    ->toggleable()
                    ->toggledHiddenByDefault(),

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
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'verified' => 'Verified',
                        'unverified' => 'Not Verified',
                        default => 'Unknown',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'verified' => 'success',
                        'unverified' => 'danger',
                        default => 'gray',
                    })

            ])

            ->defaultSort('students.created_at', 'desc')
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
                ...($user->roles->contains('name', 'Developer')
                    ? [Tables\Filters\TrashedFilter::make()]
                    : []),

                Tables\Filters\SelectFilter::make('sort_by')
                    ->label('Sort By')
                    ->options([
                        'last_name_asc' => 'Last Name (A-Z)',
                        'last_name_desc' => 'Last Name (Z-A)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return match ($data['value']) {
                            'last_name_asc' => $query
                                ->orderBy('students.last_name', 'asc')
                                ->orderBy('students.first_name', 'asc')
                                ->orderBy('students.middle_name', 'asc'),
                            'last_name_desc' => $query
                                ->orderBy('students.last_name', 'desc')
                                ->orderBy('students.first_name', 'desc')
                                ->orderBy('students.middle_name', 'desc'),
                            default => $query
                        };
                    })
            ])
            ->filtersTriggerAction(
                fn(\Filament\Tables\Actions\Action $action) =>
                $action->icon('heroicon-o-adjustments-vertical')
            )
            ->actions([

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
                    ->modalHeading('Delete Student')
                    ->modalDescription(fn(Students $record): string => "Are you sure you'd like to delete the records of " . $record->first_name . ' ' . $record->last_name . '?')
                    ->tooltip('Delete Record'),
                ...($user->roles->contains('name', 'Developer') ? [Tables\Actions\RestoreAction::make()] : [])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc'); // Sort by most recently created
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
            'view' => Pages\ViewStudents::route('/{record}/view'),
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
