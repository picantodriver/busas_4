<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SignatoriesResource\Pages;
use App\Filament\Resources\SignatoriesResource\RelationManagers;
use App\Models\Signatories;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class SignatoriesResource extends Resource
{
    protected static ?string $model = Signatories::class;
    protected static ?string $navigationGroup = 'Administrative';

    //protected static ?int $navigationSort = 10; //set the order in sidebar

    protected static ?string $navigationIcon = 'heroicon-s-pencil';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('employee_name')
                    ->label('Signatory Name')
                    ->required(),
                TextInput::make('suffix')
                    ->label('Suffix'),
                TextInput::make('employee_designation')
                    ->label('Designation'),
                Select::make('status')
                    ->label('Employee Type')
                    ->options([
                        1 => 'Permanent',
                        0 => 'COS/JO',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_name')
                    ->label('Signatory Name')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return $record->employee_name . ($record->suffix ? ', ' . $record->suffix : '');
                    }),
                TextColumn::make('employee_designation')
                    ->label('Designation')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSignatories::route('/'),
            //'create' => Pages\CreateSignatories::route('/create'),
            //'edit' => Pages\EditSignatories::route('/{record}/edit'),
        ];
    }
}
