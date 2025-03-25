<?php
namespace App\Filament\Widgets;
use Filament\Forms\Components\Select;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Students;
use App\Models\Colleges;

class SexDistributionChartWidget extends ApexChartWidget
{
    protected static ?int $sort = 1;
    protected static ?string $chartId = 'sexDistributionChart';
    protected static ?string $heading = 'Sex Distribution Per College';
    
    public ?string $selectedYear = 'All';

    protected function getFormSchema(): array
    {
        // Get years from graduation_date in students_graduation_infos
        $years = DB::table('students_graduation_infos')
            ->select(DB::raw('YEAR(graduation_date) as year'))
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->toArray();
        
        // Build the form
        return [
            Select::make('selectedYear')
                ->label('Select Year')
                ->options(array_merge(
                    ['All' => 'All'],
                    array_combine($years, $years)
                ))
                ->default('All')
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->selectedYear = $state),
        ];
    }

    protected function formatChartTitle(): string
    {
        if ($this->selectedYear === 'All') {
            return "Sex Distribution of Graduates\nAll Years (1969-2024)";
        }
        return "Sex Distribution of Graduates\nYear: {$this->selectedYear}";
    }

    protected function getData(): array
{
    // Fetch college name & abbreviation mapping
    $collegeMapping = DB::table('colleges')
        ->select('college_name', 'college_abbreviation')
        ->get()
        ->pluck('college_abbreviation', 'college_name') // Creates [college_name => college_abbreviation]
        ->toArray();

    // Fetch student data grouped by college_name
    $collegeData = DB::table('students')
        ->join('students_records', 'students.id', '=', 'students_records.student_id')
        ->join('colleges', 'students_records.college_id', '=', 'colleges.id')
        ->select('colleges.college_name', 'students.sex')
        ->whereNotNull('students.sex')
        ->where('students.sex', '<>', '')
        ->distinct('students.id') // Get distinct students to avoid counting duplicates
        ->selectRaw('COUNT(DISTINCT students.id) as count')
        ->groupBy('colleges.college_name', 'students.sex')
        ->orderBy('colleges.college_name')
        ->get()
        ->groupBy('college_name');

    // Get all college names (not abbreviations) to ensure all are included
    $allColleges = array_keys($collegeMapping);

    $colleges = [];
    $maleData = [];
    $femaleData = [];

    foreach ($allColleges as $collegeName) {
        $colleges[] = $collegeMapping[$collegeName] ?? $collegeName; // Use abbreviation if available
        $collegeStats = $collegeData->get($collegeName, collect());
        
        $male = $collegeStats->firstWhere('sex', 'M')?->count ?? 0;
        $female = $collegeStats->firstWhere('sex', 'F')?->count ?? 0;
        
        $maleData[] = $male;
        $femaleData[] = $female;
    }

    return [
        'categories' => $colleges, // Now using college_abbreviation
        'series' => [
            [
                'name' => 'Male',
                'data' => $maleData,
            ],
            [
                'name' => 'Female',
                'data' => $femaleData,
            ],
        ],
    ];
}


    protected function getOptions(): array
    {
        $data = $this->getData();
        
        return [
            'chart' => [
                'type' => 'bar',
                'height' => 450,
                'stacked' => true,
                'toolbar' => [
                    'show' => true,
                    'tools' => [
                        'download' => true,
                    ],
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false, // Changed to vertical bars
                    'dataLabels' => [
                        'total' => [
                            'enabled' => true,
                            'style' => [
                                'fontSize' => '13px',
                                'fontWeight' => 900,
                            ],
                        ],
                    ],
                    'borderRadius' => 2,
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Number of Students'
                ],
            ],
            'xaxis' => [
                'categories' => $data['categories'],
                'labels' => [
                    'rotate' => -45,
                    'style' => [
                        'fontSize' => '12px',
                    ],
                ],
            ],
            'series' => $data['series'],
            'colors' => ['#0099cb', '#FC7019'],
            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'colors' => ['#ffffff'],
                    'fontWeight' => 'bold',
                    'fontSize' => '12px',
                ],
            ],
            'legend' => [
                'position' => 'bottom',
                'horizontalAlign' => 'center',
            ],
            'tooltip' => [
                'shared' => true,
                'intersect' => false,
                'y' => [
                    'formatter' => 'function(value) { return value + " students"; }',
                ],
            ],
        ];
    }
}