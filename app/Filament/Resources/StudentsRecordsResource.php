<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentsRecordsResource\Pages;
use App\Filament\Resources\StudentsRecordsResource\RelationManagers;
use App\Models\StudentsRecords;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentsRecordsResource extends Resource
{
    protected static ?string $model = StudentsRecords::class;
    protected static ?string $navigationGroup = 'Student Information';
    protected static bool $shouldRegisterNavigation = false;

    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

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
            'index' => Pages\ListStudentsRecords::route('/'),
            //'create' => Pages\CreateStudentsRecords::route('/create'),
            //'edit' => Pages\EditStudentsRecords::route('/{record}/edit'),
        ];
    }
}
