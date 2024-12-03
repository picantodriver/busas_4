<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampusesResource\Pages;
use App\Filament\Resources\CampusesResource\RelationManagers;
use App\Models\Campuses;
use Filament\Forms;
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

    protected static ?string $navigationLabel = 'Campuses';
    protected static ?string $navigationIcon = 'heroicon-s-building-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('campus_name')->required()
                    ->label('Campus Name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('campus_name')
                    ->label('Campus Name'),
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
            'index' => Pages\ListCampuses::route('/'),
            //'create' => Pages\CreateCampuses::route('/create'),
            //'edit' => Pages\EditCampuses::route('/{record}/edit'),
        ];
    }
}
