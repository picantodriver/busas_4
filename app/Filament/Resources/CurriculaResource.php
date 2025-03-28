<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurriculaResource\Pages;
use App\Models\Programs;
use App\Models\AcadYears;
use App\Filament\Resources\CurriculaResource\RelationManagers;
use App\Models\AcadTerms;
use App\Models\Curricula;
use App\Models\ProgramsMajor;
use App\Models\Campuses;
use App\Models\Colleges;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Split;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;

use function Livewire\wrap;

class CurriculaResource extends Resource
{
    protected static ?string $model = Curricula::class;

    // protected static ?string $label = 'Curricula';
    protected static ?string $navigationGroup = 'Academic Structure';

    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationLabel= 'Curricula';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('acad_year_id')
                    ->label('Select Academic Year')
                    ->required()
                    ->options(AcadYears::all()->pluck('year', 'id'))
                    ->searchable()
                    ->reactive()
                    ->getSearchResultsUsing(fn (string $query) => AcadYears::where('year', 'like', "%{$query}%")->get()->pluck('year', 'id'))
                    ->getOptionLabelUsing(fn ($value) => AcadYears::find($value)?->year ?? 'Unknown Year'),
                Select::make('acad_term_id')
                    ->label('Select Academic Term')
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
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => $set('curricula_name', self::generateCurriculumName($get))),
                Select::make('campus_id')
                    ->label('Select Campus')
                    ->required()
                    ->reactive()
                    ->options(Campuses::all()->pluck('campus_name', 'id'))
                    ->searchable(),
                Select::make('program_id')
                    ->label('Select Program')
                    ->required()
                    ->reactive()
                    ->options(function ($get) {
                        $campus_id = $get('campus_id');
                        if ($campus_id) {
                            // get programs from campus through college
                            return Programs::whereHas('college', function (Builder $query) use ($campus_id) {
                                $query->where('campus_id', $campus_id);
                            })->pluck('program_name', 'id');
                        }
                        return [];
                    })
                    ->searchable()
                    //->getSearchResultsUsing(fn (string $query) => Programs::where('program_name', 'like', "%{$query}%")->get()->pluck('program_name', 'id'))
                    ->getOptionLabelUsing(fn ($value) => Programs::find($value)?->program_name ?? 'Unknown Program')
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => $set('curricula_name', self::generateCurriculumName($get))),
                Select::make('program_major_id')
                    ->label('Select Program Major')
                   // ->required()
                    ->reactive()
                    ->options(function ($get) {
                        $programId = $get('program_id');
                        if ($programId) {
                            return ProgramsMajor::where('program_id', $programId)->pluck('program_major_name', 'id');
                        }
                        return [];
                    })
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $query) => ProgramsMajor::where('program_major_name', 'like', "%{$query}%")->get()->pluck('program_major_name', 'id'))
                    ->getOptionLabelUsing(fn ($value) => ProgramsMajor::find($value)?->name ?? 'Unknown Program Major')
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => $set('curricula_name', self::generateCurriculumName($get))),
                TextInput::make('curricula_name')
                    ->label('Curriculum Name')
                    ->required()
                    ->reactive()
                    ->afterStateHydrated(function ($state, callable $set, callable $get) {
                        $set('curricula_name', self::generateCurriculumName($get));
                    }),
                    TableRepeater::make('courses')
                    ->relationship('courses')
                    ->label('Courses')
                    ->required()
                    ->headers([
                        Header::make('course_code')->label('Course Code')->width('150px'),
                        Header::make('descriptive_title')->label('Descriptive Title')->width('400px'),
                        Header::make('course_unit')->label('Units of Credit')->width('100px'),
                    ])
                    ->schema([
                        TextInput::make('course_code')
                            ->label('Course Code')
                            // ->required()
                            ->maxLength(255),
                        TextInput::make('descriptive_title')
                            ->label('Descriptive Title')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('course_unit')
                            ->label('Units of Credit')
                            ->required()
                            ->maxLength(10),
                    ])
                    ->columnSpanFull()
                    ->defaultItems(1)
                    ->reorderable()
                    ->addActionLabel('Add New Course')
                    
                ]);
    }

    protected static function generateCurriculumName(callable $get): string
    {
        $acadTerm = AcadTerms::find($get('acad_term_id'))?->acad_term ?? '';
        $program = Programs::find($get('program_id'))?->program_name ?? '';
        $programMajor = ProgramsMajor::find($get('program_major_id'))?->program_major_name ?? '';
        $curriculumName = $acadTerm . ', ' . $program;
        if ($programMajor) {
            $curriculumName .= ', ' . $programMajor;
        }
        return $curriculumName;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('curricula_name')
                    ->label('Curriculum')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('programs.program_name')
                    ->label('Program')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('programMajor.program_major_name')
                    ->label('Program Major')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('courses.course_code')
                    ->label('Course Code')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('courses.descriptive_title')
                    ->label('Descriptive Title')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('courses.course_unit')
                    ->label('Units of Credit')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->icon('heroicon-o-pencil-square')
                    ->tooltip('Edit Record'),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->icon('heroicon-o-trash')
                    ->modalHeading('Delete Curriculum')
                    ->modalDescription(fn(Curricula $record): string => "Are you sure you'd like to delete " . $record->curricula_name .' curriculum?')
                    ->tooltip('Delete Record'),
                    ...((auth()->guard()->user()?->roles->contains('name', 'Developer')) ? [Tables\Actions\RestoreAction::make()] : [])
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
            'index' => Pages\ListCurriculas::route('/'),
            'create' => Pages\CreateCurricula::route('/create'),
            'edit' => Pages\EditCurricula::route('/{record}/edit'),
        ];
    }
}
