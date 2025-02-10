<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Navigation\NavigationGroup;
use Filament\Widgets\Grid;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use App\Filament\Widgets\FTotalGraduatesStatWidget;
use App\Filament\Widgets\GATotalRegularGraduatesStatWidget;
use App\Filament\Widgets\GBTotalIrregularGraduatesStatWidget;
use App\Filament\Widgets\DStatsOverviewWidget;

class AdminPanelProvider extends PanelProvider
{
    protected static ?string $navigationIcon = 'heroicon-s-user';
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->collapsibleNavigationGroups(false)
            ->brandLogo(asset('storage/busas.png'))
            ->brandLogoHeight('60px')
            // ->brandName(config('app.name')) 
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => '#0099cb'
            ])
            
            ->sidebarWidth('18rem')
            ->navigationGroups([
               'Academic Structure',
               'Administrative',
               'Institutional Structure',
               'Student Information'
            ])
            
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // FTotalGraduatesStatWidget::class,
                // GATotalRegularGraduatesStatWidget::class,
                // GBTotalIrregularGraduatesStatWidget::class,
                DStatsOverviewWidget::class,
            ])
            ->plugins([
                FilamentApexChartsPlugin::make()
            ])  
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            // ->plugins([
            //     FilamentShieldPlugin::make()
            //         ->gridColumns([
            //             'default' => 1,
            //             'sm' => 2,
            //             'lg' => 3
            //         ])
            //         ->sectionColumnSpan(1)
            //         ->checkboxListColumns([
            //             'default' => 1,
            //             'sm' => 2,
            //             'lg' => 4,
            //         ])
            //         ->resourceCheckboxListColumns([
            //             'default' => 1,
            //             'sm' => 2,
            //         ]),
            // ])
            // ->plugins([

            // ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
