<?php

namespace App\Filament\Pages;

use App\Models\Agencia;
use Filament\Forms\Get;
use App\Models\Sucursal;
use Filament\Forms\Form;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;

use Filament\Forms\Components\Toggle;
use App\Filament\Widgets\StatsGeneral;
use Filament\Forms\Components\Section;
use App\Filament\Widgets\BitacoraTable;
use Filament\Forms\Components\DatePicker;
use App\Filament\Widgets\AvancesDashChart;
use App\Filament\Widgets\ClienteNuevoChart;
use App\Filament\Widgets\ClientesDashChart;
use App\Filament\Widgets\ProductosDashChart;
use App\Filament\Widgets\ServiciosDashChart;
use Filament\Pages\Dashboard as BaseDashboard;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Filament\Resources\VentaResource\Widgets\VentasNetasChart;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasPageShield;

    use BaseDashboard\Concerns\HasFiltersForm;


    // protected static ?string $title = 'Dashboard';

    public function getTitle(): string
    {
        $user = Auth::user();

        return 'Hola, ' . ($user ? $user->name : 'Invitado') . '.';
    }

    protected static ?string $navigationIcon = 'heroicon-c-presentation-chart-bar';

    // protected static string $view = 'filament.pages.dashboard-new';

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                // Section::make()
                //     ->schema([
                //         DatePicker::make('startDate')->label('Inicio'),
                //         DatePicker::make('endDate')->label('Fin'),
                //         Select::make('id')
                //             ->label('Agencia')
                //             ->options(Agencia::all()->pluck('nombre', 'id'))
                //             ->searchable()
                //         ])
                //         ->columns([
                //             'sm' => 1,
                //             'md' => 3,
                //             'xl' => 3,
                //         ])
                        // ->visible(fn(Get $get):bool => $get('activar')),
            ]);
    }

    public function getWidgets(): array
    {
        // $start = $this->filters['startDate'] == null ? now()->startOfDay() : $this->filters['startDate'] . ' 05:00:00';
        // $end = $this->filters['endDate'] == null ? now()->endOfDay() : $this->filters['endDate'] . ' 23:59:59';

        // $data = DB::table('venta_productos')
        // ->select(DB::raw('SUM(cantidad) as venta, producto_id, productos.nombre_corto as descripcion'))
        // ->join('productos', 'venta_productos.producto_id', '=', 'productos.id')
        // ->whereBetween('venta_productos.created_at', [$start, $end])
        //     ->groupBy('producto_id')
        //     ->get();

            $widgets = [
                StatsGeneral::class,
                // AvancesDashChart::class,
                // BitacoraTable::class,
            ];

        return $widgets;
    }

}
