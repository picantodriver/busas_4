<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampusesResource\Pages;
use App\Filament\Resources\CampusesResource\RelationManagers;
use App\Models\Campuses;
use App\Models\Colleges;
use App\Models\Programs;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpParser\Node\Stmt\Label;

class CampusesResource extends Resource
{
    protected static ?string $model = Campuses::class;
    protected static ?string $navigationGroup = 'Institutional Structure';

    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationLabel = 'Campus and Colleges';
    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('campus_name')->required()
                    ->label('Campus Name'),
                Repeater::make('colleges')
                ->relationship('colleges')
                ->label('Colleges')
                ->schema([
                    TextInput::make('college_name')->required()
                        ->label('College Name'),
                    TextInput::make('college_address')->required()
                        ->label('College Address'),
                    TextInput::make('college_abbreviation')->required()
                        ->label('College Abbreviation')
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('campus_name')
                    ->label('Campus Name')
                    ->searchable(),
                TextColumn::make('colleges.college_name')
                    ->label('Colleges')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->searchable(),
                TextColumn::make('colleges.college_address')
                    ->label('College Address')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->searchable(),
                TextColumn::make('colleges.college_abbreviation')
                    ->label('Campus Abbreviation')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->searchable(),
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
            'index' => Pages\ListCampuses::route('/'),
            //'create' => Pages\CreateCampuses::route('/create'),
            'edit' => Pages\EditCampuses::route('/{record}/edit'),
        ];
    }
}
