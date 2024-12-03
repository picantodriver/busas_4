<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurriculaResource\Pages;
use App\Models\Programs;
use App\Models\AcadYears;
use App\Filament\Resources\CurriculaResource\RelationManagers;
use App\Models\Curricula;
use App\Models\ProgramsMajor;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;

class CurriculaResource extends Resource
{
    protected static ?string $model = Curricula::class;
    protected static ?string $navigationGroup = 'Academic Structure';

    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationLabel= 'Curricula';

    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('acad_year_id')
                    ->label('Select Aacademic Year')
                    ->required()
                    ->options(AcadYears::all()->pluck('year', 'id'))
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $query) => AcadYears::where('year', 'like', "%{$query}%")->get()->pluck('year', 'id'))
                    ->getOptionLabelUsing(fn ($value) => AcadYears::find($value)?->year ?? 'Unknown Year'),
                Select::make('program_id')
                    ->label('Select Program')
                    ->required()
                    ->options(Programs::all()->pluck('program_name', 'id'))
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $query) => Programs::where('program_name', 'like', "%{$query}%")->get()->pluck('program_name', 'id'))
                    ->getOptionLabelUsing(fn ($value) => Programs::find($value)?->program_name ?? 'Unknown Program'),
                Select::make('program_major_id')
                    ->label('Select Program Major')
                    ->options(ProgramsMajor::all()->pluck('program_major_name', 'id'))
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $query) => ProgramsMajor::where('program_major_name', 'like', "%{$query}%")->get()->pluck('program_major_name', 'id'))
                    ->getOptionLabelUsing(fn ($value) => ProgramsMajor::find($value)?->program_major_name ?? 'Unknown Program Major'),
                TextInput::make('curriculum_name')->required(),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListCurriculas::route('/'),
            //'create' => Pages\CreateCurricula::route('/create'),
            //'edit' => Pages\EditCurricula::route('/{record}/edit'),
        ];
    }
}
