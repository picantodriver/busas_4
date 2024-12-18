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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;

class StudentsResource extends Resource
{
    protected static ?string $model = Students::class;
    protected static ?string $navigationGroup = 'Student Information';

    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationIcon = 'heroicon-s-user';

    public static function form(Form $form): Form
    {
        return $form
        // main student information section
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
        // student's registration information section
                Section::make('Student Registration Information')
                    ->description("Enter the student's registration information.")
                    ->schema([
                    TextInput::make('students_registration_infos.last_school_attended')
                        ->required()
                        ->label('Last School Attended (High School/College)'),
                    TextInput::make('students_registration_infos.last_year_attended')
                        ->label('Last Year Attended (Date graduated/last attended)')
                        ->required(),
                    TextInput::make('students_registration_infos.category')
                        ->label('Category')
                        ->required(),
                    Select::make('acad_year_id')
                        ->label('Select Academic Year')
                        ->required()
                        ->options(AcadYears::all()->pluck('year', 'id'))
                        ->searchable()
                        ->reactive()
                        ->getSearchResultsUsing(fn (string $query) => AcadYears::where('year', 'like', "%{$query}%")->get()->pluck('year', 'id'))
                        ->getOptionLabelUsing(fn ($value) => AcadYears::find($value)?->year ?? 'Unknown Year'),
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
                        ->getSearchResultsUsing(fn (string $query) => AcadTerms::where('acad_term', 'like', "%{$query}%")->get()->pluck('acad_term', 'id'))
                        ->getOptionLabelUsing(fn ($value) => AcadTerms::find($value)?->acad_term ?? 'Unknown Academic Term')
                ]),
        // student's graduation information section
                Section::make('Student Graduation Information')
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
                    ->label('Dates of Attendance (Month Year - Month')
                    ->required(),
                ])
        // student's grades and ratings for subjects taken

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
            //'create' => Pages\CreateStudents::route('/create'),
            //'edit' => Pages\EditStudents::route('/{record}/edit'),
        ];
    }
}
