<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramsResource\Pages;
use App\Filament\Resources\ProgramsResource\RelationManagers;
use App\Models\Colleges;
use App\Models\Programs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;

class ProgramsResource extends Resource
{
    protected static ?string $model = Programs::class;
    protected static ?string $navigationGroup = 'Academic Structure';
    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationLabel = 'Programs';

    protected static ?string $navigationIcon = 'heroicon-s-list-bullet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('campus_id')
                    ->label('What campus is this program under?'),

                Select::make('college_id')
                    ->label('What college is this program under?')
                    ->searchable()
                    ->options(Colleges::all()->pluck('college_name', 'id'))
                    ->getSearchResultsUsing(fn (string $query) => Colleges::where('college_name', 'like', "%{$query}%")->get()->pluck('college_name', 'id'))
                    ->required(),
                TextInput::make('program_name')->required(),
                TextInput::make('program_abbreviation')->required(),
                Repeater::make('programMajors')

                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')->required(),

                    ])
    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('program_name')
                    ->label('Program Name'),
                TextColumn::make('program_abbreviation')
                    ->label('Program Abbreviation'),
                TextColumn::make('college.college_name')
                    ->label('College Name'),
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
            'index' => Pages\ListPrograms::route('/'),
            //'create' => Pages\CreatePrograms::route('/create'),
            //'edit' => Pages\EditPrograms::route('/{record}/edit'),
        ];
    }
}
