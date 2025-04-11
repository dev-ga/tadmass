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

        $ingresos_netos = UtilsController::ingresosNetos();

        return [

            Stat::make('SEDES ATENDIDAS', 12)
                ->description(round(12) . '%')
                // ->descriptionIcon($servicios['icon'])
                // ->color($servicios['color'])
                ->extraAttributes(['class' => 'col-span-2 row-span-1 rounded-md text-center content-center']),

            Stat::make('TOTAL DE VALUACIONES', 12 . 12)
                ->description(round(12) . '%')
                // ->descriptionIcon('servicios_usd'['icon'])
                // ->color('servicios_usd'['color'])
                ->extraAttributes(['class' => 'col-span-2 row-span-1 rounded-md text-center content-center']),

            Stat::make('INGRESOS', round(12, 2))
                ->description(12 . '% ')
                // ->descriptionIcon('servicios_usd'['icon'])
                // ->color('servicios_usd'['color'])
                ->extraAttributes(['class' => 'col-span-2 row-span-1 rounded-md text-center content-center']),

            Stat::make('EGRESOS', 12)
                ->description(round(12) . '%')
                // ->descriptionIcon('label'['icon'])
                // ->color('red')
                ->extraAttributes(['class' => 'col-span-2 row-span-1 rounded-md text-center content-center']),

        ];
    }

    protected int | string | array $columnSpan = [
        // 'xs' => 3,
        'sm' => 2,
        'md' => 2,
        'xl' => 2,
    ];

}