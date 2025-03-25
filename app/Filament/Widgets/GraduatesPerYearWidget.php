<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\DB;
use App\Models\Campuses;
use App\Models\Colleges;
use App\Models\Programs;
use App\Models\Students;

class GraduatesPerYearWidget extends ApexChartWidget
{
    protected static ?int $sort = 3;
    protected static ?string $chartId = 'graduatesPerYearChart';
    protected static ?string $heading = 'Graduates Per Year (1969 - 2024)';
    // protected string|int|array $columnSpan = 'full';
    public static function canView(): bool
    {
        return false;
    }

    public ?string $selectedCampus = 'All';
    public ?string $selectedCollege = 'All';
    public ?string $selectedProgram = 'All';
    public ?string $selectedYear = 'All';
    public ?string $selectedSemester = 'All';

    protected function getFormSchema(): array
    {
        $years = DB::table('students_graduation_infos')
            ->select(DB::raw('YEAR(graduation_date) as year'))
            ->distinct()
            ->orderBy('year')
            ->pluck('year')
            ->toArray();

        return [
            Select::make('selectedCampus')
                ->label('Select Campus')
                ->options(function() {
                    return array_merge(
                        ['All' => 'All'],
                        Campuses::orderBy('campus_name')
                            ->pluck('campus_name', 'id')
                            ->toArray()
                    );
                })
                ->default('All')
                ->reactive()
                ->afterStateUpdated(function ($state) {
                    $this->selectedCampus = $state;
                    $this->selectedCollege = 'All';
                    $this->selectedProgram = 'All';
                }),

            Select::make('selectedCollege')
                ->label('Select College')
                ->options(function () {
                    if ($this->selectedCampus === 'All') {
                        return ['All' => 'All'];
                    }
                    return array_merge(
                        ['All' => 'All'],
                        Colleges::where('campus_id', $this->selectedCampus)
                            ->orderBy('college_name')
                            ->pluck('college_name', 'id')
                            ->toArray()
                    );
                })
                ->default('All')
                ->reactive()
                ->afterStateUpdated(function ($state) {
                    $this->selectedCollege = $state;
                    $this->selectedProgram = 'All';
                }),

            Select::make('selectedProgram')
                ->label('Select Program')
                ->options(function () {
                    if ($this->selectedCollege === 'All') {
                        return ['All' => 'All'];
                    }
                    return array_merge(
                        ['All' => 'All'],
                        Programs::where('college_id', $this->selectedCollege)
                            ->orderBy('program_name')
                            ->pluck('program_name', 'id')
                            ->toArray()
                    );
                })
                ->default('All')
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->selectedProgram = $state),

            Select::make('selectedYear')
                ->label('Select Year')
                ->options(array_merge(
                    ['All' => 'All'],
                    array_combine($years, $years)
                ))
                ->default('All')
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->selectedYear = $state),

            Select::make('selectedSemester')
                ->label('Select Semester')
                ->options([
                    'All' => 'All',
                    '1' => '1st Semester',
                    '2' => '2nd Semester',
                    '3' => 'Summer',
                ])
                ->default('All')
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->selectedSemester = $state),
        ];
    }

    protected function getData(): array
    {
        $query = Students::query()
            ->join('students_records', 'students.id', '=', 'students_records.student_id')
            ->join('students_graduation_infos', 'students.id', '=', 'students_graduation_infos.student_id')
            ->join('programs', 'students_records.program_id', '=', 'programs.id')
            ->join('colleges', 'programs.college_id', '=', 'colleges.id')
            ->join('campuses', 'colleges.campus_id', '=', 'campuses.id');

        if ($this->selectedCampus !== 'All') {
            $query->where('campuses.id', $this->selectedCampus);
        }

        if ($this->selectedCollege !== 'All') {
            $query->where('colleges.id', $this->selectedCollege);
        }

        if ($this->selectedProgram !== 'All') {
            $query->where('programs.id', $this->selectedProgram);
        }

        if ($this->selectedYear !== 'All') {
            $query->whereYear('students_graduation_infos.graduation_date', $this->selectedYear);
        }

        if ($this->selectedSemester !== 'All') {
            $query->where('students_graduation_infos.semester', $this->selectedSemester);
        }

        $graduatesData = $query
            ->select(DB::raw('YEAR(students_graduation_infos.graduation_date) as year'))
            ->selectRaw('COUNT(DISTINCT students.id) as total')
            ->groupBy(DB::raw('YEAR(students_graduation_infos.graduation_date)'))
            ->orderBy('year')
            ->pluck('total', 'year')
            ->toArray();

        return $graduatesData;
    }

    protected function getOptions(): array
    {
        $data = $this->getData();

        return [
            'chart' => [
                'type' => 'area', 
                'height' => 300,
                'fontFamily' => 'Inter, sans-serif',
            ],
            'series' => [
                [
                    'name' => "Graduates",
                    'data' => array_values($data),
                ],
            ],
            'xaxis' => [
                'categories' => array_keys($data),
                'tickAmount' => 10, 
                'labels' => [
                    'style' => [
                        'fontFamily' => 'Inter, sans-serif',
                    ],
                    'rotate' => -45, 
                    'show' => true,
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'Inter, sans-serif',
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => false, 
                'style' => [
                'fontFamily' => 'Inter, sans-serif',
            ],
            ],
            'colors' => ['#0099cb'],
            'stroke' => [
                'width' => 2, 
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'light',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#00b6e4'],
                    'inverseColors' => false,
                    'opacityFrom' => 0.7,
                    'opacityTo' => 0.3,
                    'stops' => [0, 90, 100],
                ],
            ],
            'title' => [
            'text' => "Graduates Per Year (1969 - 2024)",
            'align' => 'center',
            'style' => [
                'fontWeight' => 'bold',
                'fontFamily' => 'Inter, sans-serif',
                'color' => '#263238',
            ],
        ],
        ];
    }
}