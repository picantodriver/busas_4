<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DStatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalGraduates = $this->getTotalGraduates();
        $totalRegularGraduates = $this->getTotalRegularGraduates();
        $totalIrregularGraduates = $this->getTotalIrregularGraduates();

        return [
            Stat::make('Total Graduates (2024)', $totalGraduates)
                ->icon('heroicon-o-academic-cap')
                ->extraAttributes(['class' => 'flex justify-between items-center space-x-2 rtl:flex-row-reverse']),
            // Stat::make('Total Graduates (2024)', $totalRegularGraduates)
            //     ->icon('heroicon-o-trophy')
            //     ->extraAttributes(['class' => 'flex justify-between items-center space-x-2 rtl:flex-row-reverse']),
            // Stat::make('Total Graduates (2024)', $totalIrregularGraduates)
            //     ->icon('heroicon-o-clock')
            //     ->extraAttributes(['class' => 'flex justify-between items-center space-x-2 rtl:flex-row-reverse']),
        ];
    }

    protected function getTotalGraduates(): int
    {
        // Sample data for total graduates
        $data = [
            2024 => 6500,
        ];

        return $data[2024] ?? 0;
    }

    protected function getTotalRegularGraduates(): int
    {
        // Sample data for total regular graduates
        $data = [
            2024 => 5000,
        ];

        return $data[2024] ?? 0;
    }

    protected function getTotalIrregularGraduates(): int
    {
        // Sample data for total irregular graduates
        $data = [
            2024 => 1500,
        ];

        return $data[2024] ?? 0;
    }
}