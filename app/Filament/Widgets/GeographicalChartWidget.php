<?php

namespace App\Filament\Widgets;

use Filament\Forms\Components\Select;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class GeographicalChartWidget extends ApexChartWidget
{
    protected static ?int $sort = 4;
    protected static ?string $chartId = 'graduateStudentsChart';
    protected static ?string $heading = 'Number of Graduate Students Per Province';

    public ?string $selectedYear = '2024';
    public ?string $selectedProvince = 'All';
    public ?string $selectedMunicipality = 'All';

    public static function canView(): bool
    {
        return false;
    }
    

    // Sample data for Bicol region provinces and municipalities
    protected array $bicolData = [
        'Albay' => [
            'Legazpi City' => ['2023' => 450, '2024' => 475, '2025' => 500],
            'Tabaco City' => ['2023' => 320, '2024' => 340, '2025' => 360],
            'Ligao City' => ['2023' => 280, '2024' => 300, '2025' => 315],
            'Daraga' => ['2023' => 250, '2024' => 270, '2025' => 290],
            'Guinobatan' => ['2023' => 200, '2024' => 220, '2025' => 240],
            'Camalig' => ['2023' => 190, '2024' => 210, '2025' => 230],
            'Tiwi' => ['2023' => 170, '2024' => 190, '2025' => 210],
            'Oas' => ['2023' => 160, '2024' => 180, '2025' => 200],
            'Polangui' => ['2023' => 150, '2024' => 170, '2025' => 190],
            'Malilipot' => ['2023' => 140, '2024' => 160, '2025' => 180],
            'Sto. Domingo' => ['2023' => 130, '2024' => 150, '2025' => 170],
        ],
        'Camarines Sur' => [
            'Naga City' => ['2023' => 520, '2024' => 550, '2025' => 580],
            'Iriga City' => ['2023' => 280, '2024' => 300, '2025' => 320],
            'Pili' => ['2023' => 250, '2024' => 270, '2025' => 290],
            'Calabanga' => ['2023' => 240, '2024' => 260, '2025' => 280],
            'Ragay' => ['2023' => 230, '2024' => 250, '2025' => 270],
            'Goa' => ['2023' => 220, '2024' => 240, '2025' => 260],
            'Bula' => ['2023' => 210, '2024' => 230, '2025' => 250],
            'Nabua' => ['2023' => 200, '2024' => 220, '2025' => 240],
            'Caramoan' => ['2023' => 190, '2024' => 210, '2025' => 230],
            'Tigaon' => ['2023' => 180, '2024' => 200, '2025' => 220],
            'Libmanan' => ['2023' => 170, '2024' => 190, '2025' => 210],
        ],
        'Camarines Norte' => [
            'Daet' => ['2023' => 250, '2024' => 270, '2025' => 290],
            'Labo' => ['2023' => 180, '2024' => 200, '2025' => 220],
            'Jose Panganiban' => ['2023' => 170, '2024' => 190, '2025' => 210],
            'Mercedes' => ['2023' => 160, '2024' => 180, '2025' => 200],
            'Paracale' => ['2023' => 150, '2024' => 170, '2025' => 190],
            'Capalonga' => ['2023' => 140, '2024' => 160, '2025' => 180],
            'San Vicente' => ['2023' => 130, '2024' => 150, '2025' => 170],
            'Basud' => ['2023' => 120, '2024' => 140, '2025' => 160],
        ],
        'Sorsogon' => [
            'Sorsogon City' => ['2023' => 380, '2024' => 400, '2025' => 420],
            'Bulan' => ['2023' => 220, '2024' => 240, '2025' => 260],
            'Castilla' => ['2023' => 200, '2024' => 220, '2025' => 240],
            'Juban' => ['2023' => 180, '2024' => 200, '2025' => 220],
            'Casiguran' => ['2023' => 160, '2024' => 180, '2025' => 200],
            'Irosin' => ['2023' => 150, '2024' => 170, '2025' => 190],
            'Gubat' => ['2023' => 140, '2024' => 160, '2025' => 180],
            'Barcelona' => ['2023' => 130, '2024' => 150, '2025' => 170],
            'Pilar' => ['2023' => 120, '2024' => 140, '2025' => 160],
            'Magallanes' => ['2023' => 110, '2024' => 130, '2025' => 150],
        ],
        'Catanduanes' => [
            'Virac' => ['2023' => 200, '2024' => 220, '2025' => 240],
            'San Andres' => ['2023' => 180, '2024' => 200, '2025' => 220],
            'Caramoran' => ['2023' => 160, '2024' => 180, '2025' => 200],
            'Pandan' => ['2023' => 140, '2024' => 160, '2025' => 180],
            'Viga' => ['2023' => 130, '2024' => 150, '2025' => 170],
            'Bagamanoc' => ['2023' => 120, '2024' => 140, '2025' => 160],
            'Baras' => ['2023' => 110, '2024' => 130, '2025' => 150],
            'Gigmoto' => ['2023' => 100, '2024' => 120, '2025' => 140],
            'Panganiban' => ['2023' => 90, '2024' => 110, '2025' => 130],
            'Bato' => ['2023' => 80, '2024' => 100, '2025' => 120],
        ],
        'Masbate' => [
            'Masbate City' => ['2023' => 280, '2024' => 300, '2025' => 320],
            'Aroroy' => ['2023' => 250, '2024' => 270, '2025' => 290],
            'Baleno' => ['2023' => 230, '2024' => 250, '2025' => 270],
            'Balud' => ['2023' => 210, '2024' => 230, '2025' => 250],
            'Cataingan' => ['2023' => 190, '2024' => 210, '2025' => 230],
            'Cawayan' => ['2023' => 170, '2024' => 190, '2025' => 210],
            'Dimasalang' => ['2023' => 160, '2024' => 180, '2025' => 200],
            'Esperanza' => ['2023' => 150, '2024' => 170, '2025' => 190],
            'Mandaon' => ['2023' => 140, '2024' => 160, '2025' => 180],
            'Milagros' => ['2023' => 130, '2024' => 150, '2025' => 170],
            'Monreal' => ['2023' => 120, '2024' => 140, '2025' => 160],
            'San Fernando' => ['2023' => 110, '2024' => 130, '2025' => 150],
            'San Jacinto' => ['2023' => 100, '2024' => 120, '2025' => 140],
            'San Pascual' => ['2023' => 90, '2024' => 110, '2025' => 130],
            'Uson' => ['2023' => 80, '2024' => 100, '2025' => 120],
        ],
    ];

    protected function getFormSchema(): array
    {
        return [
            Select::make('selectedYear')
                ->label('Select Year')
                ->options([
                    '2023' => '2023',
                    '2024' => '2024',
                    '2025' => '2025',
                ])
                ->default('2024')
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->selectedYear = $state),

            Select::make('selectedProvince')
                ->label('Select Province')
                ->options(array_merge(
                    ['All' => 'All'],
                    array_combine(
                        array_keys($this->bicolData),
                        array_keys($this->bicolData)
                    )
                ))
                ->default('All')
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->selectedProvince = $state),

            Select::make('selectedMunicipality')
                ->label('Select Municipality')
                ->options(function () {
                    if ($this->selectedProvince === 'All') {
                        return ['All' => 'All'];
                    }

                    return array_merge(
                        ['All' => 'All'],
                        array_combine(
                            array_keys($this->bicolData[$this->selectedProvince]),
                            array_keys($this->bicolData[$this->selectedProvince])
                        )
                    );
                })
                ->default('All')
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->selectedMunicipality = $state),
        ];
    }

    protected function getData(): array
    {
        $year = $this->selectedYear ?? '2024';

        if ($this->selectedProvince === 'All') {
            $data = [];
            foreach ($this->bicolData as $province => $municipalities) {
                $total = 0;
                foreach ($municipalities as $municipality => $yearData) {
                    $total += $yearData[$year] ?? 0;
                }
                $data[$province] = $total;
            }
            return $data;
        }

        if ($this->selectedMunicipality === 'All') {
            $municipalities = $this->bicolData[$this->selectedProvince];
            $data = [];
            foreach ($municipalities as $municipality => $yearData) {
                $data[$municipality] = $yearData[$year] ?? 0;
            }
            return $data;
        }

        return [
            $this->selectedMunicipality => $this->bicolData[$this->selectedProvince][$this->selectedMunicipality][$year] ?? 0,
        ];
    }

    protected function getOptions(): array
    {
        $data = $this->getData();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'fontFamily' => 'Inter, sans-serif',
            ],
            'series' => [
                [
                    'name' => "Graduate Students",
                    'data' => array_values($data),
                ],
            ],
            'xaxis' => [
                'categories' => array_keys($data),
                'labels' => [
                    'style' => [
                       'fontFamily' => 'Inter, sans-serif',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'Inter, sans-serif',
                    ],
                ],
            ],
            'colors' => ['#0099cb'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => true,
                ],
            ],
            'title' => [
            'text' => "Graduate Students in Bicol Region ({$this->selectedYear})",
            'align' => 'center',
            'style' => [
                'fontWeight' => 'bold',
                'fontFamily' => 'Inter, sans-serif',
                'color' => '#263238',
            ],
        ],
        'dataLabels' => [
            'style' => [
                'fontFamily' => 'Inter, sans-serif',
            ],
        ],

            'plugins' => [
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'backgroundColor' => 'var(--chart-tooltip-background)',
                    'titleColor' => 'var(--chart-tooltip-text)',
                    'bodyColor' => 'var(--chart-tooltip-text)',
                ],
                'legend' => [
                    'labels' => [
                        'color' => 'var(--chart-legend-text)',
                    ],
                ],
            ],
        ];
    }
}

//With printer icon

// <?php

// namespace App\Filament\Widgets;

// use Filament\Forms\Components\Select;
// use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
// use Illuminate\Support\Facades\Route;

// class GeographicalChartWidget extends ApexChartWidget
// {
//     protected static ?string $chartId = 'graduateStudentsChart';
//     protected static ?string $heading = 'Number of Graduate Students Per Province';

//     public ?string $selectedYear = '2024';
//     public ?string $selectedProvince = 'All';
//     public ?string $selectedMunicipality = 'All';

//     // Sample data for Bicol region provinces and municipalities
//     protected array $bicolData = [
//         'Albay' => [
//             'Legazpi City' => ['2023' => 450, '2024' => 475, '2025' => 500],
//             'Tabaco City' => ['2023' => 320, '2024' => 340, '2025' => 360],
//             'Ligao City' => ['2023' => 280, '2024' => 300, '2025' => 315],
//             'Daraga' => ['2023' => 250, '2024' => 270, '2025' => 290],
//             'Guinobatan' => ['2023' => 200, '2024' => 220, '2025' => 240],
//             'Camalig' => ['2023' => 190, '2024' => 210, '2025' => 230],
//             'Tiwi' => ['2023' => 170, '2024' => 190, '2025' => 210],
//             'Oas' => ['2023' => 160, '2024' => 180, '2025' => 200],
//             'Polangui' => ['2023' => 150, '2024' => 170, '2025' => 190],
//             'Malilipot' => ['2023' => 140, '2024' => 160, '2025' => 180],
//             'Sto. Domingo' => ['2023' => 130, '2024' => 150, '2025' => 170],
//         ],
//         'Camarines Sur' => [
//             'Naga City' => ['2023' => 520, '2024' => 550, '2025' => 580],
//             'Iriga City' => ['2023' => 280, '2024' => 300, '2025' => 320],
//             'Pili' => ['2023' => 250, '2024' => 270, '2025' => 290],
//             'Calabanga' => ['2023' => 240, '2024' => 260, '2025' => 280],
//             'Ragay' => ['2023' => 230, '2024' => 250, '2025' => 270],
//             'Goa' => ['2023' => 220, '2024' => 240, '2025' => 260],
//             'Bula' => ['2023' => 210, '2024' => 230, '2025' => 250],
//             'Nabua' => ['2023' => 200, '2024' => 220, '2025' => 240],
//             'Caramoan' => ['2023' => 190, '2024' => 210, '2025' => 230],
//             'Tigaon' => ['2023' => 180, '2024' => 200, '2025' => 220],
//             'Libmanan' => ['2023' => 170, '2024' => 190, '2025' => 210],
//         ],
//         'Camarines Norte' => [
//             'Daet' => ['2023' => 250, '2024' => 270, '2025' => 290],
//             'Labo' => ['2023' => 180, '2024' => 200, '2025' => 220],
//             'Jose Panganiban' => ['2023' => 170, '2024' => 190, '2025' => 210],
//             'Mercedes' => ['2023' => 160, '2024' => 180, '2025' => 200],
//             'Paracale' => ['2023' => 150, '2024' => 170, '2025' => 190],
//             'Capalonga' => ['2023' => 140, '2024' => 160, '2025' => 180],
//             'San Vicente' => ['2023' => 130, '2024' => 150, '2025' => 170],
//             'Basud' => ['2023' => 120, '2024' => 140, '2025' => 160],
//         ],
//         'Sorsogon' => [
//             'Sorsogon City' => ['2023' => 380, '2024' => 400, '2025' => 420],
//             'Bulan' => ['2023' => 220, '2024' => 240, '2025' => 260],
//             'Castilla' => ['2023' => 200, '2024' => 220, '2025' => 240],
//             'Juban' => ['2023' => 180, '2024' => 200, '2025' => 220],
//             'Casiguran' => ['2023' => 160, '2024' => 180, '2025' => 200],
//             'Irosin' => ['2023' => 150, '2024' => 170, '2025' => 190],
//             'Gubat' => ['2023' => 140, '2024' => 160, '2025' => 180],
//             'Barcelona' => ['2023' => 130, '2024' => 150, '2025' => 170],
//             'Pilar' => ['2023' => 120, '2024' => 140, '2025' => 160],
//             'Magallanes' => ['2023' => 110, '2024' => 130, '2025' => 150],
//         ],
//         'Catanduanes' => [
//             'Virac' => ['2023' => 200, '2024' => 220, '2025' => 240],
//             'San Andres' => ['2023' => 180, '2024' => 200, '2025' => 220],
//             'Caramoran' => ['2023' => 160, '2024' => 180, '2025' => 200],
//             'Pandan' => ['2023' => 140, '2024' => 160, '2025' => 180],
//             'Viga' => ['2023' => 130, '2024' => 150, '2025' => 170],
//             'Bagamanoc' => ['2023' => 120, '2024' => 140, '2025' => 160],
//             'Baras' => ['2023' => 110, '2024' => 130, '2025' => 150],
//             'Gigmoto' => ['2023' => 100, '2024' => 120, '2025' => 140],
//             'Panganiban' => ['2023' => 90, '2024' => 110, '2025' => 130],
//             'Bato' => ['2023' => 80, '2024' => 100, '2025' => 120],
//         ],
//         'Masbate' => [
//             'Masbate City' => ['2023' => 280, '2024' => 300, '2025' => 320],
//             'Aroroy' => ['2023' => 250, '2024' => 270, '2025' => 290],
//             'Baleno' => ['2023' => 230, '2024' => 250, '2025' => 270],
//             'Balud' => ['2023' => 210, '2024' => 230, '2025' => 250],
//             'Cataingan' => ['2023' => 190, '2024' => 210, '2025' => 230],
//             'Cawayan' => ['2023' => 170, '2024' => 190, '2025' => 210],
//             'Dimasalang' => ['2023' => 160, '2024' => 180, '2025' => 200],
//             'Esperanza' => ['2023' => 150, '2024' => 170, '2025' => 190],
//             'Mandaon' => ['2023' => 140, '2024' => 160, '2025' => 180],
//             'Milagros' => ['2023' => 130, '2024' => 150, '2025' => 170],
//             'Monreal' => ['2023' => 120, '2024' => 140, '2025' => 160],
//             'San Fernando' => ['2023' => 110, '2024' => 130, '2025' => 150],
//             'San Jacinto' => ['2023' => 100, '2024' => 120, '2025' => 140],
//             'San Pascual' => ['2023' => 90, '2024' => 110, '2025' => 130],
//             'Uson' => ['2023' => 80, '2024' => 100, '2025' => 120],
//         ],
//     ];
//     protected function getFormSchema(): array
//     {
//         return [
//             Select::make('selectedYear')
//                 ->label('Select Year')
//                 ->options([
//                     '2023' => '2023',
//                     '2024' => '2024',
//                     '2025' => '2025',
//                 ])
//                 ->default('2024')
//                 ->reactive()
//                 ->afterStateUpdated(fn ($state) => $this->selectedYear = $state),

//             Select::make('selectedProvince')
//                 ->label('Select Province')
//                 ->options(array_merge(
//                     ['All' => 'All'],
//                     array_combine(
//                         array_keys($this->bicolData),
//                         array_keys($this->bicolData)
//                     )
//                 ))
//                 ->default('All')
//                 ->reactive()
//                 ->afterStateUpdated(fn ($state) => $this->selectedProvince = $state),

//             Select::make('selectedMunicipality')
//                 ->label('Select Municipality')
//                 ->options(function () {
//                     if ($this->selectedProvince === 'All') {
//                         return ['All' => 'All'];
//                     }

//                     return array_merge(
//                         ['All' => 'All'],
//                         array_combine(
//                             array_keys($this->bicolData[$this->selectedProvince]),
//                             array_keys($this->bicolData[$this->selectedProvince])
//                         )
//                     );
//                 })
//                 ->default('All')
//                 ->reactive()
//                 ->afterStateUpdated(fn ($state) => $this->selectedMunicipality = $state),
//         ];
//     }

//     protected function getData(): array
//     {
//         $year = $this->selectedYear ?? '2024';

//         if ($this->selectedProvince === 'All') {
//             $data = [];
//             foreach ($this->bicolData as $province => $municipalities) {
//                 $total = 0;
//                 foreach ($municipalities as $municipality => $yearData) {
//                     $total += $yearData[$year] ?? 0;
//                 }
//                 $data[$province] = $total;
//             }
//             return $data;
//         }

//         if ($this->selectedMunicipality === 'All') {
//             $municipalities = $this->bicolData[$this->selectedProvince];
//             $data = [];
//             foreach ($municipalities as $municipality => $yearData) {
//                 $data[$municipality] = $yearData[$year] ?? 0;
//             }
//             return $data;
//         }

//         return [
//             $this->selectedMunicipality => $this->bicolData[$this->selectedProvince][$this->selectedMunicipality][$year] ?? 0,
//         ];
//     }



// protected function getOptions(): array
// {
//     $data = $this->getData();

//     return [
//         'chart' => [
//             'type' => 'bar',
//             'height' => 300,
//             'toolbar' => [
//                 'show' => true,
//                 'tools' => [
//                     'download' => false,
//                     'customIcons' => [
//                         [
//                             'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
//                                 <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
//                             </svg>',
//                             'title' => 'Print as CSV',
//                             'class' => '',
//                             'click' => 'function() { window.location.href = "/export/csv"; }',
//                         ],
//                     ],
//                 ],
//             ],
//         ],
//         'series' => [
//             [
//                 'name' => "Graduate Students",
//                 'data' => array_values($data),
//             ],
//         ],
//         'xaxis' => [
//             'categories' => array_keys($data),
//             'labels' => [
//                 'style' => [
//                     'fontFamily' => 'inherit',
//                 ],
//             ],
//         ],
//         'yaxis' => [
//             'labels' => [
//                 'style' => [
//                     'fontFamily' => 'inherit',
//                 ],
//             ],
//         ],
//         'colors' => ['#0099cb'],
//         'plotOptions' => [
//             'bar' => [
//                 'borderRadius' => 3,
//                 'horizontal' => true,
//             ],
//         ],
//         'title' => [
//             'text' => "Graduate Students in Bicol Region ({$this->selectedYear})",
//             'align' => 'center',
//             'floating' => true,
//             'offsetY' => 0,
//             'style' => [
//                 'fontSize' => '16px',
//                 'fontWeight' => 'bold',
//                 'color' => '#263238',
//             ],
//         ],
//     ];
// }
// }

// Route::get('export/csv', function () {
//     // Your CSV export logic here...
//     $data = [
//         // Example data
//         ['Province', 'City', 'Students'],
//         ['Albay', 'Legazpi City', 475],
//         ['Albay', 'Tabaco City', 340],
//         // Add more data as needed
//     ];

//     $output = fopen('php://output', 'w');
//     foreach ($data as $row) {
//         fputcsv($output, $row);
//     }
//     fclose($output);

//     header('Content-Type: text/csv');
//     header('Content-Disposition: attachment; filename="graduate_students.csv"');
//     exit;
// })->name('export.csv');