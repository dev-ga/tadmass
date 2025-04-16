<?php

namespace App\Filament\Widgets;

use Filament\Forms;
use App\Models\Cita;
use App\Models\Gasto;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\TasaBcv;
use App\Models\Producto;
use Filament\Forms\Form;
use App\Models\Disponible;
use App\Models\Frecuencia;
use Filament\Actions\Action;
use App\Models\VentaProducto;
use App\Models\VentaServicio;
use App\Models\DetalleAsignacion;
use App\Http\Controllers\StatController;
use App\Http\Controllers\UtilsController;

use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;


class StatsGeneral extends BaseWidget
{
    use InteractsWithPageFilters;

    use HasWidgetShield;

    protected static ?int $sort = 1;

    // protected int | string | array $columnSpan = '2';




    protected function getStats(): array
    {

        $ingresos           = UtilsController::ingresos();
        $egresos            = UtilsController::egresos();
        $producto_max_venta = UtilsController::producto_max_venta();

        return [

            Stat::make('TOTAL INGRESOS EN DOLARES US$', number_format($ingresos, 2).' US$')
                ->description(round(12) . '%')
                // ->descriptionIcon($servicios['icon'])
                // ->color($servicios['color'])
                ->extraAttributes(['class' => 'col-span-1 row-span-1 rounded-md text-center content-center']),

            Stat::make('TOTAL EGRESOS EN DOLARES US$', number_format($egresos, 2) . ' US$')
                ->description(round(12) . '%')
                // ->descriptionIcon($servicios['icon'])
                // ->color($servicios['color'])
                ->extraAttributes(['class' => 'col-span-1 row-span-1 rounded-md text-center content-center']),

            Stat::make('VENTA NETA EN DOLARES US$', number_format($ingresos - $egresos, 2) . ' US$')
                ->description(12 . '% ')
                // ->descriptionIcon('servicios_usd'['icon'])
                // ->color('servicios_usd'['color'])
                ->extraAttributes(['class' => 'col-span-1 row-span-1 rounded-md text-center content-center']),

            Stat::make('PRODUCTO MAS VENDIDO', $producto_max_venta)
                ->description(round(12) . '%')
                // ->descriptionIcon('label'['icon'])
                // ->color('red')
                ->extraAttributes(['class' => 'col-span-1 row-span-1 rounded-md text-center content-center']),

        ];
    }

    // protected int | string | array $columnSpan = [ 
    //     // 'xs' => 3,
    //     'sm' => 2,
    //     'md' => 4,
    //     'xl' => 4,
    // ];

}
