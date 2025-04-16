<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentColor::register([
            'azul'         => Color::hex('#04C4D9'),
            'azulClaro'    => Color::hex('#1ED9C6'),
            'verdeOscuro'  => Color::hex('#238C3D'),
            'verdeClaro'   => Color::hex('#49F262'),
            'negro'        => Color::hex('#0D0D0D'),
            'disabled'     => Color::hex('#A9A9A9'),
            'danger'       => Color::hex('#b30000'),
        ]);

        FilamentView::registerRenderHook(
            PanelsRenderHook::FOOTER,
            function () {
                return view('footer');
            }
        );
    }
}
