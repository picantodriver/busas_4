<?php

namespace App\Filament\Widgets;

use App\Models\Students;
use Filament\Tables;
use App\Filament\Resources\StudentsResource;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class NewEntries extends BaseWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Students::query()
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Student Name')
                    // ->searchable()
                    ->sortable()
                    ->size('sm')
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        $lastName = $state ? '<strong>' . $state . '</strong>, ' : '';
                        $otherNames = array_filter([
                            $record->first_name,
                            $record->middle_name
                        ]);
                        return $lastName . implode(' ', $otherNames);
                    }),
                Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->formatStateUsing(fn(string $state): string => match ($state) {
                    'verified' => 'Verified',
                    'unverified' => 'Not Verified',
                    default => 'Unknown',
                })
                ->color(fn(string $state): string => match ($state) {
                    'verified' => 'success',
                    'unverified' => 'danger',
                    default => 'gray',
                }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Created')
                    ->dateTime('M d, Y')
                    ->size('sm')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_by')
                ->label('Created By')
                ->formatStateUsing(fn ($state) => \App\Models\User::find($state)?->initials ?? 'System')
                ->size('sm'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->actions([])->headerActions([ 
                Tables\Actions\Action::make('view_all')
                    ->label('View All')
                    ->url(fn(): string => StudentsResource::getUrl())
            ]);
    }
}