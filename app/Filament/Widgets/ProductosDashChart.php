<?php

namespace App\Filament\Widgets;

use App\Models\Frecuencia;
use Flowframe\Trend\Trend;
use App\Models\VentaProducto;

// use Carbon\Carbon;
use App\Models\VentaServicio;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ProductosDashChart extends ChartWidget
{
    use HasWidgetShield;

    use InteractsWithPageFilters;

    protected static ?string $heading = 'Productos';

    protected static ?string $maxHeight = '250px';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = '1';

    protected function getData(): array
    {
        // $start = $this->filters['startDate'] == null ? now()->startOfDay() : $this->filters['startDate'] . ' 05:00:00';
        // $end = $this->filters['endDate'] == null ? now()->endOfDay() : $this->filters['endDate'] . ' 23:59:59';

        // $data = DB::table('venta_productos')
        //     ->select(DB::raw('SUM(cantidad) as venta, producto_id, productos.nombre_corto as descripcion'))
        //     ->join('productos', 'venta_productos.producto_id', '=', 'productos.id')
        //     ->whereBetween('venta_productos.created_at', [$start, $end])
        //     ->groupBy('producto_id')
        //     ->get();

        // $labels = $data->map(fn($data) => $data->descripcion);
        // $totalVentas = $data->sum('venta');
        // $percentages = $data->map(fn($item) => round(($item->venta / $totalVentas) * 100, 2));

        // $labelsWithPercentages = $labels->map(function ($label, $index) use ($percentages) {
        //     return $label . ' - (' . $percentages[$index] . '%)';
        // });

        return [
            'datasets' => [
                [
                    'label' => 'Average de Productos',
                    // 'data' => $data->map(fn($data) => $data->venta),
                    'data' => [2,3,4,5,6],
                    'backgroundColor' => [
                        '#a16d69',
                        '#99bcbf',
                        '#bf99a9',
                        '#bfaf99',
                        '#99a9bf',
                        '#99bfaf',
                        '#9c99bf',
                        '#99bf9c',
                        '#bf9c99',
                        '#bf99bc',
                        '#c7a8a5',
                        '#ab7e7a',
                        '#7ba69d',
                        '#7b9aa6',
                        '#a6877b',
                        '#7b85a6',
                        '#a69d7b',
                        '#a67b85',
                        '#9aa67b',
                        '#7ba687',
                        '#a67b9a',
                        '#56737f'
                    ],
                    // 'borderColor' => '#22c55e',
                    // 'fill' => true,
                ],
            ],
            // 'labels' => $labelsWithPercentages->toArray(),
            // 'percentages' => $percentages,
        ];
    }

    public function getDescription(): ?string
    {
        return 'Productos vendidos';
    }

    protected static ?array $options = [
        'scales' => [
            'x' => [
                'display' => false,
            ],
            'y' => [
                'display' => false,
            ],
        ],
        'plugins' => [
            'legend' => [
                'position' => 'left',
                'align' => 'start',
            ],
        ],
    ];   // protected function getOptions(): RawJs

    protected function getType(): string
    {
        return 'pie';
    }


    //how to hide ChartWidget for particular condition in filamentphp v3?

}
