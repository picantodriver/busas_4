<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollegesResource\Pages;
use App\Filament\Resources\CollegesResource\RelationManagers;
use App\Models\Colleges;
use App\Models\Campuses;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CollegesResource extends Resource
{
    protected static ?string $model = Colleges::class;
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationGroup = 'Institutional Structure';

    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string$navigationLabel = 'Colleges';

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('campus_id')
                    ->label('Select Campus')
                    ->required()
                    ->options(Campuses::all()->pluck('campus_name', 'id'))
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $query) => Campuses::where('campus_name', 'like', "%{$query}%")->get()->pluck('campus_name', 'id'))
                    ->getOptionLabelUsing(fn ($value) => Campuses::find($value)?->campus_name ?? 'Unknown Campus'),
                TextInput::make('college_name')
                    ->required(),
                TextInput::make('college_address')
                    ->required(),
                TextInput::make('college_abbreviation')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('college_name')
                    ->label('College Name'),
                TextColumn::make('Campus.campus_name')
                    ->label('Campus Name'),
                TextColumn::make('college_address')
                    ->label('College Address'),
                TextColumn::make('college_abbreviation')
                    ->label('Campus Abbreviation'),
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
            'index' => Pages\ListColleges::route('/'),
            //'create' => Pages\CreateColleges::route('/create'),
            //'edit' => Pages\EditColleges::route('/{record}/edit'),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
