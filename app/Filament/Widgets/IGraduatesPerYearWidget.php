<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Filament\Forms\Components\Select;

class IGraduatesPerYearWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'graduatesPerYearChart';
    protected static ?string $heading = 'Graduates Per Year (1969 - 2024)';
    protected string|int|array $columnSpan = 'full';

    public ?string $selectedCampus = 'All';
    public ?string $selectedCollege = 'All';
    public ?string $selectedProgram = 'All';
    public ?string $selectedYear = 'All';
    public ?string $selectedSemester = 'All';

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

            Select::make('selectedSemester')
                ->label('Select Semester')
                ->options([
                    'All' => 'All',
                    '1st Semester' => '1st Semester',
                    '2nd Semester' => '2nd Semester',
                    'Summer' => 'Summer',
                ])
                ->default('All')
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->selectedSemester = $state),
        ];
    }

    protected function getData(): array
    {
        // Sample data for graduates per year from 1969 to 2024
        $data = [
            1969 => 1120, 1970 => 1255, 1971 => 1900, 1972 => 1330, 1973 => 1230,
            1974 => 1715, 1975 => 1380, 1976 => 1290, 1977 => 1190, 1978 => 1195,
            1979 => 1100, 1980 => 1105, 1981 => 1110, 1982 => 1115, 1983 => 1120,
            1984 => 1125, 1985 => 1130, 1986 => 1135, 1987 => 1410, 1988 => 1145,
            1989 => 1510, 1990 => 1155, 1991 => 2160, 1992 => 2165, 1993 => 1970,
            1994 => 2175, 1995 => 1820, 1996 => 2185, 1997 => 1290, 1998 => 1295,
            1999 => 2200, 2000 => 2205, 2001 => 1210, 2002 => 1215, 2003 => 1220,
            2004 => 2225, 2005 => 2320, 2006 => 1235, 2007 => 1240, 2008 => 1245,
            2009 => 2950, 2010 => 2255, 2011 => 1260, 2012 => 1265, 2013 => 1270,
            2014 => 2275, 2015 => 2280, 2016 => 2835, 2017 => 2290, 2018 => 2295,
            2019 => 3900, 2020 => 2305, 2021 => 3910, 2022 => 3315, 2023 => 3120,
            2024 => 3325,
        ];
        
        if ($this->selectedCampus !== 'All' || $this->selectedCollege !== 'All' || $this->selectedProgram !== 'All' || $this->selectedYear !== 'All' || $this->selectedSemester !== 'All') {
        }

        return $data;
    }

    protected function getOptions(): array
    {
        $data = $this->getData();

        return [
            'chart' => [
                'type' => 'area', 
                'height' => 300,
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
                        'fontFamily' => 'inherit',
                    ],
                    'rotate' => -45, 
                    'show' => true,
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => false, 
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
            ],
        ];
    }
}