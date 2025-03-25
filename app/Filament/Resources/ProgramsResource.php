<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramsResource\Pages;
use App\Filament\Resources\ProgramsResource\RelationManagers;
use App\Models\Colleges;
use App\Models\Programs;
use App\Models\Campuses;
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
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;

class ProgramsResource extends Resource
{
    protected static ?string $model = Programs::class;
    protected static ?string $navigationGroup = 'Academic Structure';
    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationLabel = 'Programs and Majors';
    protected static ?string $navigationIcon = 'heroicon-s-list-bullet';

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
            Select::make('college_id')
                ->label('What college is this program under?')
                ->searchable()
                ->options(function ($get) {
                    $campusId = $get('campus_id');
                    if ($campusId) {
                        return Colleges::where('campus_id', $campusId)->pluck('college_name', 'id');
                    }
                    return [];
                })
                ->getSearchResultsUsing(function ($get, string $query) {
                    $campusId = $get('campus_id');
                    if ($campusId) {
                        return Colleges::where('campus_id', $campusId)
                            ->where('college_name', 'like', "%{$query}%")
                            ->pluck('college_name', 'id');
                    }
                    return [];
                })
                ->required(),
                TextInput::make('program_name')->required(),
                TextInput::make('program_abbreviation')->required(),
                TableRepeater::make('programMajors')
                    ->relationship('programMajors')
                    ->label('Program Majors')
                    ->headers([
                        Header::make('program_major_name')->label('Program Major')->width('200px'),
                        Header::make('program_major_abbreviation')->label('Program Major Abbreviation')->width('200px'),
                    ])
                    ->schema([
                        TextInput::make('program_major_name')
                            ->label('Program Major'),
                        TextInput::make('program_major_abbreviation')
                            ->label('Program Major Abbreviation ')
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('program_name')
                    ->label('Program Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('program_abbreviation')
                    ->label('Program Abbreviation')
                    ->searchable(),
                TextColumn::make('college.college_name')
                    ->label('College Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('programMajors.program_major_name')
                    // ->getStateUsing(function ($record) {
                    //     return $record->programMajors->pluck('program_major_name')->join(', ');
                    // })
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->searchable(),
                TextColumn::make('programMajors.program_major_abbreviation')
                    // ->label('Program Major Abbreviation')
                    // ->getStateUsing(function ($record) {
                    //     return $record->programMajors->pluck('program_major_abbreviation')->join(', ');
                    // })
                    ->listWithLineBreaks()
                    ->searchable()
                    ->bulleted(),
                // TextColumn::make('status')
                //     ->label('Status')
                //     ->badge()
                //     ->formatStateUsing(fn(string $state): string => match ($state) {
                //         'verified' => 'Verified',
                //         'unverified' => 'Not Verified',
                //         default => 'Unknown',
                //     })
                //     ->color(fn(string $state): string => match ($state) {
                //         'verified' => 'success',
                //         'unverified' => 'danger',
                //         default => 'gray',
                //     })
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->icon('heroicon-o-pencil-square')
                    ->tooltip('Edit Record'),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->icon('heroicon-o-trash')
                    ->modalHeading('Delete Program and Major')
                    ->modalDescription(fn(Programs $record): string => "Are you sure you'd like to delete " . $record->program_name .' program?')
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
            'index' => Pages\ListPrograms::route('/'),
            'create' => Pages\CreatePrograms::route('/create'),
            'edit' => Pages\EditPrograms::route('/{record}/edit'),
        ];
    }

}
