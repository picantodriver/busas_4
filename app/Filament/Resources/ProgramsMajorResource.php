<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramsMajorResource\Pages;
use App\Filament\Resources\ProgramsMajorResource\RelationManagers;
use App\Models\ProgramsMajor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgramsMajorResource extends Resource
{
    protected static ?string $model = ProgramsMajor::class;
    protected static ?string $navigationGroup = 'Academic Structure';
    protected static bool $shouldRegisterNavigation = true;

    //protected static ?int $navigationSort = 10; //set the order in sidebar
    protected static ?string $navigationLabel = 'Program Majors';
    protected static ?string $navigationIcon = 'heroicon-s-check-badge';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
