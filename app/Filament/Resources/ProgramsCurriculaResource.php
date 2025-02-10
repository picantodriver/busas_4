<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramsCurriculaResource\Pages;
use App\Filament\Resources\ProgramsCurriculaResource\RelationManagers;
use App\Models\ProgramsCurricula;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgramsCurriculaResource extends Resource
{
    protected static ?string $model = ProgramsCurricula::class;

    protected static bool $shouldRegisterNavigation = false;
    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationLabel = 'Program Curriculum (REMOVE)';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

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
            'index' => Pages\ListProgramsCurriculas::route('/'),
            'create' => Pages\CreateProgramsCurricula::route('/create'),
            'edit' => Pages\EditProgramsCurricula::route('/{record}/edit'),
        ];
    }
}
