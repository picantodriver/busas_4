<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurriculaAcadTermsResource\Pages;
use App\Filament\Resources\CurriculaAcadTermsResource\RelationManagers;
use App\Models\CurriculaAcadTerms;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class CurriculaAcadTermsResource extends Resource
{

    protected static ?string $model = CurriculaAcadTerms::class;

    protected static bool $shouldRegisterNavigation = false;
    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationLabel = 'Curriculum Academic (REMOVE)';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
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
            'index' => Pages\ListCurriculaAcadTerms::route('/'),
            'create' => Pages\CreateCurriculaAcadTerms::route('/create'),
            'edit' => Pages\EditCurriculaAcadTerms::route('/{record}/edit'),
        ];
    }
}
