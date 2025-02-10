<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramsMajorResource\Pages;
use App\Filament\Resources\ProgramsMajorResource\RelationManagers;
use App\Models\ProgramsMajor;
use App\Models\Programs;
use App\Models\Campuses;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class ProgramsMajorResource extends Resource
{
    protected static ?string $model = ProgramsMajor::class;
    protected static ?string $navigationGroup = 'Academic Structure';

    protected static bool $shouldRegisterNavigation = false;

    //protected static ?int $navigationSort = 10; //set the order in sidebar
    protected static ?string $navigationLabel = 'Program Majors';
    protected static ?string $navigationIcon = 'heroicon-s-check-badge';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('campus_id')
                    ->label('Select Campus')
                    ->required()
                    ->options(Campuses::all()->pluck('campus_name', 'id'))
                    ->searchable()
                    ->reactive()
                    ->getSearchResultsUsing(fn (string $query) => Campuses::where('campus_name', 'like', "%{$query}%")->get()->pluck('campus_name', 'id'))
                    ->getOptionLabelUsing(fn ($value) => Campuses::find($value)?->campus_name ?? 'Unknown Campus'),
                Select::make('program_id')
                    ->label('Select Program')
                    ->required()
                    ->options(Programs::all()->pluck('program_name', 'id'))
                    ->searchable()
                    ->reactive()
                    ->getSearchResultsUsing(fn (string $query) => Programs::where('program_name', 'like', "%{$query}%")->get()->pluck('program_name', 'id'))
                    ->getOptionLabelUsing(fn ($value) => Programs::find($value)?->program_name ?? 'Unknown Program'),
                TextInput::make('program_major_name')
                    ->label('Major Name')
                    ->required(),
                TextInput::make('program_major_abbreviation')
                    ->label('Major Abbreviation')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('program.program_name')
                    ->label('Program Name')
                    ->searchable(),
                TextColumn::make('program_major_name'),
                TextColumn::make('program_major_abbreviation')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProgramsMajors::route('/'),
            'create' => Pages\CreateProgramsMajor::route('/create'),
            'edit' => Pages\EditProgramsMajor::route('/{record}/edit'),
        ];
    }
}
