<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcadTermsResource\Pages;
use App\Filament\Resources\AcadTermsResource\RelationManagers;
use App\Models\AcadTerms;
use App\Models\AcadYears;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcadTermsResource extends Resource
{

    protected static ?string $model = AcadTerms::class;
    protected static ?string $navigationGroup = 'Institutional Structure';
    protected static bool $shouldRegisterNavigation = false;
    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationLabel = 'Academic Terms';

    protected static ?string $navigationIcon = 'heroicon-s-calendar-date-range';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('acad_year_id')
                    ->live()
                    ->label('Select Academic Year')
                    ->required()
                    ->options(fn () => AcadYears::pluck('year', 'id'))
                    ->searchable()
                    ->native(false)
                    ->getSearchResultsUsing(fn (string $query) => AcadYears::where('year', 'like', "%{$query}%")->get()->pluck('year', 'id'))
                    ->getOptionLabelUsing(fn ($value) => AcadYears::find($value)?->year ?? 'Unknown Year'),
                Select::make('acad_term')
                    ->label('Academic Term')
                    ->required()
                    ->options(function($get) {
                        $acadYear = AcadYears::find($get('acad_year_id'))?->year;
                        return [
                            '1st Semester ' . $acadYear => '1st Semester ' . $acadYear,
                            '2nd Semester ' . $acadYear => '2nd Semester ' . $acadYear,
                            'Midyear '  . explode('-', $acadYear)[1] => 'Midyear ' . explode('-', $acadYear)[1],
                            'Summer '  . explode('-', $acadYear)[1] => 'Summer ' . explode('-', $acadYear)[1],
                        ];
                    })
                    ->native(false)
                    ->visible(fn ($get) => $get('acad_year_id')),
                //TextInput::make('acad_term')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('AcadYear.year')
                    ->label('Academic Year'),
                TextColumn::make('acad_term')
                    ->label('Academic Term'),
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

    /**
     * Get the pages of the resource.
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcadTerms::route('/'),
            //'create' => Pages\CreateAcadTerms::route('/create'),
            //'edit' => Pages\EditAcadTerms::route('/{record}/edit'),
        ];
    }
}
