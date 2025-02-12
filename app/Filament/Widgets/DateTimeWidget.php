<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Carbon\Carbon;

class DateTimeWidget extends Widget
{
    protected static string $view = 'filament.widgets.date-time-widget';
    
    protected static bool $isLazy = false;
    protected static bool $isStateless = true;

    protected static bool $shouldRegisterNavigation = false;
    
    protected static ?int $sort = null;  

    public static function canView(): bool
    {
        return false;
    }

    protected function getViewData(): array
    {
        return [
            'currentDateTime' => Carbon::now('Asia/Manila')->format('l, d/m/Y h:i:s A'),

        ];
    }
}
