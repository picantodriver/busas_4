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
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\View\PanelsRenderHook;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->collapsibleNavigationGroups(false)
            ->sidebarCollapsibleOnDesktop()
            ->brandLogo(asset('storage/busas_logo_light.png'))
            ->brandLogoHeight('60px')
            ->darkModeBrandLogo(asset('storage/busas_logo_dark.png'))
            // ->brandName(config('app.name')) 
            ->maxContentWidth('full')
            //->topbar(false)
            // ->renderHook(
            //     PanelsRenderHook::TOPBAR,
            //     fn () => view('filament.widgets.date-time-widget')
            // )
            ->default()
            ->id('admin')
            ->path('admin')
            ->passwordReset()
            ->emailVerification()
            ->login()
            ->colors([
                'primary' => ('#019bdb'),
            ])
            ->navigationGroups([
                'Academic Structure',
                'Administrative',
                'Institutional Structure',
                'Student Information'
            ])
            ->renderHook(
                // PanelsRenderHook::FOOTER,
                PanelsRenderHook::BODY_END,
                fn() => view('footer')
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}