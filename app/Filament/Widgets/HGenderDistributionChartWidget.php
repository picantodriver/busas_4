<?php

namespace App\Filament\Widgets;

use Filament\Forms\Components\Select;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class HGenderDistributionChartWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'sexDistributionChart';
    protected static ?string $heading = 'Gender Distribution of Graduates Per Year (1969 - 2024)';

    public ?string $selectedYear = 'All';
    public ?string $selectedCampus = 'All';
    public ?string $selectedCollege = 'All';
    public ?string $selectedProgram = 'All';

    protected array $campusData = [
        'Main Campus' => [
            'Graduate School', 'Open University', 'College of Law', 'College of Arts and Letters',
            'Jesse M. Robredo Institute of Governance and Development', 'College of Nursing', 'College of Science',
            'Institute of Physical Education, Sports and Recreation'
        ],
        'Daraga Campus' => [
            'College of Dental Medicine', 'College of Business, Economics and Management', 'College of Social Sciences and Philosophy',
            'College of Education', 'College of Medicine'
        ],
        'East Campus' => [
            'College of Engineering', 'Institute of Design and Architecture', 'College of Industrial Technology'
        ],
        'Satellite Campus' => [
            'Bicol University Guinobatan', 'Bicol University Gubat', 'Bicol University Polangui', 'Bicol University Tabaco'
        ],
    ];

    protected array $collegePrograms = [
        'Graduate School' => ['Program A', 'Program B', 'Program C'],
        'Open University' => ['Program D', 'Program E', 'Program F'],
        'College of Law' => ['Program G', 'Program H', 'Program I'],
        'College of Arts and Letters' => ['Program J', 'Program K', 'Program L'],
        'Jesse M. Robredo Institute of Governance and Development' => ['Program M', 'Program N', 'Program O'],
        'College of Nursing' => ['Program P', 'Program Q', 'Program R'],
        'College of Science' => ['Program S', 'Program T', 'Program U'],
        'Institute of Physical Education, Sports and Recreation' => ['Program V', 'Program W', 'Program X'],
        'College of Dental Medicine' => ['Program Y', 'Program Z', 'Program AA'],
        'College of Business, Economics and Management' => ['Program BB', 'Program CC', 'Program DD'],
        'College of Social Sciences and Philosophy' => ['Program EE', 'Program FF', 'Program GG'],
        'College of Education' => ['Program HH', 'Program II', 'Program JJ'],
        'College of Medicine' => ['Program KK', 'Program LL', 'Program MM'],
        'College of Engineering' => ['Program NN', 'Program OO', 'Program PP'],
        'Institute of Design and Architecture' => ['Program QQ', 'Program RR', 'Program SS'],
        'College of Industrial Technology' => ['Program TT', 'Program UU', 'Program VV'],
        'Bicol University Guinobatan' => ['Program WW', 'Program XX', 'Program YY'],
        'Bicol University Gubat' => ['Program ZZ', 'Program AAA', 'Program BBB'],
        'Bicol University Polangui' => ['Program CCC', 'Program DDD', 'Program EEE'],
        'Bicol University Tabaco' => ['Program FFF', 'Program GGG', 'Program HHH'],
    ];

    protected function getFormSchema(): array
    {
        return [
            Select::make('selectedYear')
                ->label('Select Year')
                ->options(array_merge(
                    ['All' => 'All'],
                    array_combine(
                        range(1969, 2024),
                        range(1969, 2024)
                    )
                ))
                ->default('All')
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->selectedYear = $state),

            Select::make('selectedCampus')
                ->label('Select Campus')
                ->options(array_merge(
                    ['All' => 'All'],
                    array_combine(
                        array_keys($this->campusData),
                        array_keys($this->campusData)
                    )
                ))
                ->default('All')
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->selectedCampus = $state),

            Select::make('selectedCollege')
                ->label('Select College')
                ->options(function () {
                    if ($this->selectedCampus === 'All') {
                        return ['All' => 'All'];
                    }

                    return array_merge(
                        ['All' => 'All'],
                        array_combine(
                            $this->campusData[$this->selectedCampus],
                            $this->campusData[$this->selectedCampus]
                        )
                    );
                })
                ->default('All')
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->selectedCollege = $state),

            Select::make('selectedProgram')
                ->label('Select Program')
                ->options(function () {
                    if ($this->selectedCollege === 'All') {
                        return ['All' => 'All'];
                    }

                    return array_merge(
                        ['All' => 'All'],
                        array_combine(
                            $this->collegePrograms[$this->selectedCollege],
                            $this->collegePrograms[$this->selectedCollege]
                        )
                    );
                })
                ->default('All')
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->selectedProgram = $state),
        ];
    }
    protected function formatChartTitle(): string
    {
        $components = [];
        
        // Add non-"All" selections to components array
        if ($this->selectedYear !== 'All') {
            $components[] = "Year: {$this->selectedYear}";
        }
        if ($this->selectedCampus !== 'All') {
            $components[] = $this->selectedCampus;
        }
        if ($this->selectedCollege !== 'All') {
            $components[] = $this->selectedCollege;
        }
        if ($this->selectedProgram !== 'All') {
            $components[] = $this->selectedProgram;
        }

        // If everything is "All", return a simple title
        if (empty($components)) {
            return "Gender Distribution of Graduates\nAll Years (1969-2024)";
        }

        $totalLength = array_sum(array_map('strlen', $components));

        if ($totalLength <= 50) {
            return "Gender Distribution of Graduates\n" . implode(' | ', $components);
        } elseif ($totalLength <= 100) {
            $halfPoint = ceil(count($components) / 2);
            $firstLine = array_slice($components, 0, $halfPoint);
            $secondLine = array_slice($components, $halfPoint);
            
            return "Gender Distribution of Graduates\n" . 
                   implode(' | ', $firstLine) . "\n" .
                   implode(' | ', $secondLine);
        } else {
            return "Gender Distribution of Graduates\n" . implode("\n", $components);
        }
    }

    protected function getData(): array
    {
        // Sample data for gender distribution of graduates per year from 1969 to 2024
        $data = [
            'years' => range(1969, 2024),
            'male' => [
                30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85, 90, 95, 100, 105, 110, 115, 120, 125, 130, 135, 140, 145, 150, 155, 160, 165, 170, 175, 180, 185, 190, 195, 200, 205, 210, 215, 220, 225, 230, 235, 240, 245, 250, 255, 260, 265, 270, 275, 280, 285, 290, 295, 300, 305,
            ],
            'female' => [
                25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85, 90, 95, 100, 105, 110, 115, 120, 125, 130, 135, 140, 145, 150, 155, 160, 165, 170, 175, 180, 185, 190, 195, 200, 205, 210, 215, 220, 225, 230, 235, 240, 245, 250, 255, 260, 265, 270, 275, 280, 285, 290, 295, 300,
            ],
        ];

        if ($this->selectedYear === 'All') {
            return [
                'male' => array_sum($data['male']),
                'female' => array_sum($data['female']),
                'selectedYear' => 'All Years'
            ];
        } else {
            $yearIndex = array_search((int)$this->selectedYear, $data['years']);
            return [
                'male' => $data['male'][$yearIndex],
                'female' => $data['female'][$yearIndex],
                'selectedYear' => $this->selectedYear
            ];
        }
    }

    protected function getOptions(): array
    {
        $data = $this->getData();

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
                'toolbar' => [
                    'show' => true,
                    'tools' => [
                        'download' => true,
                        'selection' => false,
                        'zoom' => false,
                        'zoomin' => false,
                        'zoomout' => false,
                        'pan' => false,
                        'reset' => false,
                        'customIcons' => [],
                    ],
                ],
            ],
            'series' => [
                $data['male'],
                $data['female'],
            ],
            'labels' => ['Male', 'Female'],
            'colors' => ['#0099cb', '#ff4560'],
            'fill' => [
                'opacity' => 0.5,
            ],
            'stroke' => [
                'show' => true,
                'width' => 1,
                'colors' => ['#0099cb', '#ff4560'], 
            ],
            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'colors' => ['#ffffff'], 
                    'fontWeight' => 'normal', 
                    'textShadow' => 'none', 
                ],
            ],
            'legend' => [
                'position' => 'bottom',
            ],
            'title' => [
                'text' => $this->formatChartTitle(),
                'align' => 'center',
            ],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size' => '50%',
                    ],
                ],
            ],
            'grid' => [
                'show' => true,
                'borderColor' => '#e7e7e7',
                'strokeDashArray' => 4,
                'position' => 'back',
                'xaxis' => [
                    'lines' => [
                        'show' => false,
                    ],
                ],
                'yaxis' => [
                    'lines' => [
                        'show' => true, 
                    ],
                ],
            ],
        ];
    }
}