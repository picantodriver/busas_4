<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoursesResource\Pages;
use App\Filament\Resources\CoursesResource\RelationManagers;
use App\Models\Courses;
use App\Models\Curricula;
use App\Models\Campuses;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoursesResource extends Resource
{
    protected static ?string $model = Courses::class;

    protected static ?string $navigationGroup = 'Academic Structure';

    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string$navigationLabel = 'Courses';

    protected static ?string $navigationIcon = 'heroicon-s-paint-brush';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Select::make('curricula_id')
                    ->required()
                    ->label('Select Curriculum')
                    ->options(Curricula::all()->pluck('curricula_name', 'id'))
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $query) => Curricula::where('curricula_name', 'like', "%{$query}%")->get()->pluck('curricula_name', 'id'))
                    ->getOptionLabelUsing(fn ($value) => Curricula::find($value)?->curricula_name ?? 'Unknown Curriculum'),
                TextInput::make('course_code')
                    ->label('Course Code')
                    ->required(),
                TextInput::make('descriptive_title')
                    ->label('Descriptive Title')
                    ->required(),
                TextInput::make('course_unit')
                    ->label('Units of Credit')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('curriculum_name')
                ->label('Curriculum Name')
                ->searchable()
                ->sortable(),
            TextColumn::make('acadYear.year')
                ->label('Academic Year')
                ->searchable()
                ->sortable(),
            TextColumn::make('acadTerm.acad_term')
                ->label('Academic Term')
                ->searchable()
                ->sortable(),
            TextColumn::make('program.program_name')
                ->label('Program')
                ->searchable()
                ->sortable(),
            TextColumn::make('programMajor.name')
                ->label('Program Major')
                ->searchable()
                ->sortable(),
            TextColumn::make('college.college_name')
                ->label('College')
                ->searchable()
                ->sortable(),
            TextColumn::make('campus.campus_name')
                ->label('Campus')
                ->searchable()
                ->sortable(),
            TextColumn::make('courses')
                ->label('Courses')
                ->getStateUsing(function ($record) {
                    return $record->courses->pluck('course_code')->join(', ');
                }),
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
            'index' => Pages\ListCourses::route('/'),
            //'create' => Pages\CreateCourses::route('/create'),
            //'edit' => Pages\EditCourses::route('/{record}/edit'),
        ];
    }
}
