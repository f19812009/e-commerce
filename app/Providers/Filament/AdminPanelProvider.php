<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use App\Filament\Widgets\ColorfulStatsOverviewWidget;
use App\Filament\Widgets\SalesTrendChartWidget;
use App\Filament\Widgets\OrdersStatusChartWidget;
use App\Filament\Widgets\LatestOrdersWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => [
                    50 => '236, 253, 245',
                    100 => '209, 250, 229',
                    200 => '167, 243, 208',
                    300 => '110, 231, 183',
                    400 => '52, 211, 153',
                    500 => '16, 185, 129',
                    600 => '15, 76, 58',
                    700 => '11, 57, 43',
                    800 => '11, 43, 34',
                    900 => '8, 30, 24',
                    950 => '4, 18, 14',
                ],
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): string => view('filament.custom-head')->render(),
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): string => view('filament.custom-topbar')->render(),
            )
            ->brandName(fn (): string => \App\Models\DashboardSetting::current()['brand_name'] ?? 'Al-Aqsa Mart')
            ->brandLogo(fn (): string => \App\Models\DashboardSetting::logoUrl())
            ->favicon(fn (): string => \App\Models\DashboardSetting::faviconUrl())
            ->brandLogoHeight('3.25rem')
            ->navigationGroups([
                'Dashboard Report',
                'Shop Management',
                'Inventory & Stock',
                'Accounts & Finance',
                'Settings & Appearance',
            ])
            ->navigationItems([
                \Filament\Navigation\NavigationItem::make('Visit Website (ওয়েবসাইট)')
                    ->url(fn (): string => url('/'), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-globe-alt')
                    ->sort(1),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                ColorfulStatsOverviewWidget::class,
                SalesTrendChartWidget::class,
                OrdersStatusChartWidget::class,
                LatestOrdersWidget::class,
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
