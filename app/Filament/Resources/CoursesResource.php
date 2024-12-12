<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoursesResource\Pages;
use App\Filament\Resources\CoursesResource\RelationManagers;
use App\Models\Courses;
use App\Models\Campuses;
use App\Models\Programs;
use App\Models\ProgramsMajor;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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
                Select::make('campus_id')
                    ->label('Select Campus')
                    ->required()
                    ->reactive()
                    ->options(Campuses::all()->pluck('campus_name', 'id'))
                    ->searchable(),
                Select::make('program_id')
                    ->label('Select Program')
                    ->required()
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
                    ->getOptionLabelUsing(fn ($value) => Programs::find($value)?->program_name ?? 'Unknown Program'),
                Select::make('program_major_id')
                    ->label('Select Program Major')
                   // ->required()
                    ->options(function ($get) {
                        $programId = $get('program_id');
                        if ($programId) {
                            return ProgramsMajor::where('program_id', $programId)->pluck('program_major_name', 'id');
                        }
                        return [];
                    })
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $query) => ProgramsMajor::where('program_major_name', 'like', "%{$query}%")->get()->pluck('program_major_name', 'id'))
                    ->getOptionLabelUsing(fn ($value) => ProgramsMajor::find($value)?->name ?? 'Unknown Program Major'),
                TextInput::make('descriptive_title')
                    ->label('Descriptive Title')
                    ->required(),
                TextInput::make('course_code')
                    ->label('Course Code')
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
                TextColumn
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
