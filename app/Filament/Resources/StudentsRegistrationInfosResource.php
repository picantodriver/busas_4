<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentsRegistrationInfosResource\Pages;
use App\Filament\Resources\StudentsRegistrationInfosResource\RelationManagers;
use App\Models\StudentsRegistrationInfos;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentsRegistrationInfosResource extends Resource
{
    protected static ?string $model = StudentsRegistrationInfos::class;
    protected static ?string $navigationGroup = 'Student Information';
    protected static bool $shouldRegisterNavigation = false;
    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationIcon = 'heroicon-s-pencil-square';

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
            'index' => Pages\ListStudentsRegistrationInfos::route('/'),
            //'create' => Pages\CreateStudentsRegistrationInfos::route('/create'),
            //'edit' => Pages\EditStudentsRegistrationInfos::route('/{record}/edit'),
        ];
    }
}
