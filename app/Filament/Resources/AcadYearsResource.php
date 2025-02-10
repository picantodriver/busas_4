<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcadYearsResource\Pages;
use App\Filament\Resources\AcadYearsResource\RelationManagers;
use App\Models\AcadYears;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcadYearsResource extends Resource
{
    protected static ?string $model = AcadYears::class;
    protected static ?string $navigationGroup = 'Institutional Structure';

    //protected static ?int $navigationSort = 1; //set the order in sidebar
    protected static ?string $navigationLabel = 'Academic Years and Terms';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('year')->required(),
                DatePicker::make('start_date')->required(),
                DatePicker::make('end_date')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('year'),
                TextColumn::make('start_date')
                ->sortable(),
                TextColumn::make('end_date'),
                TextColumn::make('AcadTerms.acad_term')
                ->label('Academic Terms')
                ->listWithLineBreaks()
                ->bulleted(),
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
            'index' => Pages\ListAcadYears::route('/'),
            //'create' => Pages\CreateAcadYears::route('/create'),
            //'edit' => Pages\EditAcadYears::route('/{record}/edit'),
        ];
    }
}
