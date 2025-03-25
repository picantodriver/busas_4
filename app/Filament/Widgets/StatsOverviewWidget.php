<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Student;
use App\Models\Curricula;
use App\Models\Students;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 0;
    protected function getCards(): array
    {
        return [
            Card::make(
                'Total Undergraduate Students',
                Students::where('student_type', 'undergraduate')->count()
            ),

            Card::make(
                'Total Graduate Students',
                Students::where('student_type', 'graduate')->count()
            ),

            Card::make('No. of Unreviewed Entries', Students::where('status', 'unverified')->whereNull('deleted_at')->count()),

            Card::make('Total Programs', \App\Models\Curricula::count()),
        ];
    }
}
