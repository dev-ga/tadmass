<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use ReflectionClass;

use Filament\Tables\View\TablesRenderHook as ViewTablesRenderHook;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // $panelHooks = new ReflectionClass(PanelsRenderHook::class);
        // // Table Hooks
        // $tableHooks = new ReflectionClass(ViewTablesRenderHook::class);
        // // Widget Hooks
        // $widgetHooks = new ReflectionClass(Widgets\View\WidgetsRenderHook::class);

        // $panelHooks = $panelHooks->getConstants();
        // $tableHooks = $tableHooks->getConstants();
        // $widgetHooks = $widgetHooks->getConstants();

        // foreach ($panelHooks as $hook) {
        //     $panel->renderHook($hook, function () use ($hook) {
        //         return Blade::render('<div style="border: solid red 1px; padding: 2px;">{{ $name }}</div>', [
        //             'name' => Str::of($hook)->remove('tables::'),
        //         ]);
        //     });
        // }
        // foreach ($tableHooks as $hook) {
        //     $panel->renderHook($hook, function () use ($hook) {
        //         return Blade::render('<div style="border: solid red 1px; padding: 2px;">{{ $name }}</div>', [
        //             'name' => Str::of($hook)->remove('tables::'),
        //         ]);
        //     });
        // }
        // foreach ($widgetHooks as $hook) {
        //     $panel->renderHook($hook, function () use ($hook) {
        //         return Blade::render('<div style="border: solid red 1px; padding: 2px;">{{ $name }}</div>', [
        //             'name' => Str::of($hook)->remove('tables::'),
        //         ]);
        //     });
        // }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->passwordReset()
            ->profile()
            ->colors([
                'primary' => '#1ED9C6'
            ])
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
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->renderHook(PanelsRenderHook::TOPBAR_END, function () {
                return view('topbar-end-bcv');
            })
            // ->renderHook(PanelsRenderHook::PAGE_HEADER_WIDGETS_BEFORE, function () {
            //     return view('bcv');
            // })
            ->sidebarFullyCollapsibleOnDesktop()
            ->brandLogo(asset('images/tadmasLogo.png'))
            ->darkModeBrandLogo(asset('images/tadmasLogoWhite.png'))
            ->brandLogoHeight('3.5rem')
            ->databaseNotifications();
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_HEADER_WIDGETS_BEFORE,
            fn () => view('bcv'),
            scopes: Pages\Dashboard::class,
        );
    }
}