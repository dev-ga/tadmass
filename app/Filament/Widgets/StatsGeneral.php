<?php

namespace App\Filament\Widgets;

use App\Models\Cita;
use App\Models\Gasto;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\TasaBcv;
use App\Models\Producto;
use App\Models\Disponible;
use App\Models\Frecuencia;
use App\Models\VentaProducto;
use App\Models\VentaServicio;
use App\Models\DetalleAsignacion;
use App\Http\Controllers\StatController;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

use Filament\Forms\Form;
use Filament\Forms;
use Filament\Actions\Action;


class StatsGeneral extends BaseWidget
{
    use InteractsWithPageFilters;

    use HasWidgetShield;

    protected static ?int $sort = 1;

    // protected int | string | array $columnSpan = '2';




    protected function getStats(): array
    {

        // $start = $this->filters['startDate'] == null ? now()->startOfDay() : $this->filters['startDate'].' 05:00:00';
        // $end = $this->filters['endDate'] == null ? now()->endOfDay() : $this->filters['endDate'].' 23:59:59';
        // $agencia_id = $this->filters['agencia_id'] == null ? null : $this->filters['agencia_id'];

        // $servicios              = StatController::servicios_facturados($start, $end, $sucursal_id =  null);
        // $servicios_usd          = StatController::total_servicios_usd($start, $end, $sucursal_id =  null);
        // $promedio               = StatController::promedio_servicio_cliente($start, $end, $sucursal_id =  null);

        // $productos              = StatController::productos_facturados($start, $end, $sucursal_id =  null);
        // $productos_usd          = StatController::total_productos_usd($start, $end, $sucursal_id =  null);
        // $promedio_prod          = StatController::promedio_productos_cliente($start, $end, $sucursal_id =  null);

        return [

            /**
             * GRUPO 1 SERVICIOS:
             * -----------
             */

            //Stat Servicios -----------------------------------------------------------------------------------------------
            //--------------------------------------------------------------------------------------------------------------

            // Stat::make('Usuarios activos', 125)
            // ->extraItems([
            //     Action::make('agregarUsuario')
            //         ->label('Agregar usuario')
            //         ->button()
            //         ->color('primary')
            //         ->form([
            //             Forms\Components\TextInput::make('nombre')
            //                 ->label('Nombre')
            //                 ->required(),
            //         ])
            //         ->modalHeading('Registrar nuevo usuario')
            //         ->action(function (array $data) {
            //             \App\Models\User::create([
            //                 'name' => $data['nombre'],
            //                 'email' => $data['email'],
            //                 'telefono' => $data['telefono'] ?? null,
            //                 'password' => bcrypt('password'), // solo ejemplo
            //             ]);
            //         })
            //         ->successNotificationTitle('Usuario creado correctamente')
            //         ->modalSubmitActionLabel('Guardar')
            //         ->modalCancelActionLabel('Cancelar'),
            // ]),
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


            //Stat Productos -----------------------------------------------------------------------------------------------
            //--------------------------------------------------------------------------------------------------------------
            Stat::make('EGRESOS', 12)
                ->description(round(12) . '%')
                // ->descriptionIcon('label'['icon'])
                // ->color('red')
                ->extraAttributes(['class' => 'col-span-2 row-span-1 rounded-md text-center content-center']),

            // Stat::make('TOTAL PRODUCTOS($)', $productos_usd['total_productos_hoy'] . $productos_usd['letra'])
            //     ->description(round($productos_usd['porcentaje']) . '%')
            //     ->descriptionIcon($productos_usd['icon'])
            //     ->color($productos_usd['color'])
            //     ->extraAttributes(['class' => 'col-span-1 row-span-1 rounded-md text-center border-4 border-[#7B9AA6] content-center']),

            // Stat::make('PROMEDIO PRODUCTO/CLIENTE', number_format($promedio_prod['promedio_hoy'], 1))
            //     ->description(round($promedio_prod['porcentaje']) . '%')
            //     ->descriptionIcon($promedio_prod['icon'])
            //     ->color($promedio_prod['color'])
            //     ->extraAttributes(['class' => 'col-span-1 row-span-1 rounded-md text-center border-4 border-[#7B9AA6] content-center']),

        ];
    }

    protected int | string | array $columnSpan = [
        // 'xs' => 3,
        'sm' => 2,
        'md' => 2,
        'xl' => 2,
    ];

}
