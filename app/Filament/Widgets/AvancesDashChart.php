<?php

namespace App\Filament\Widgets;

use App\Models\Cita;
use App\Models\Frecuencia;
use Flowframe\Trend\Trend;

// use Carbon\Carbon;
use App\Models\VentaProducto;
use App\Models\VentaServicio;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class AvancesDashChart extends ChartWidget
{
    use InteractsWithPageFilters;

    use HasWidgetShield;

    protected static ?string $heading = '% de Avances';

    protected static ?string $maxHeight = '250px';

    protected static ?int $sort = 4;

    // protected int | string | array $columnSpan = '1';

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

        $labels = [
            'Avance',
            'Restante',
        ];

        // dd($array, $labels);
        // dd($data);

        // $shortenedLabels = $labels->map(function($label) {
        //     return substr($label, 0, 10) . (strlen($label) > 10 ? '...' : '');
        // });

        return [
            'datasets' => [
                [
                    'label' => '',
                    'data' => [2, 5],
                    'backgroundColor' => [
                        '#04C4D9',
                        '#238C3D',
                    ],
                    'borderColor' => '#ffff',
                ],

            ],
            'labels' => $labels,
            // 'labels' => $labelsWithPercentages->toArray(),
            // 'percentages' => $percentages,

        ];
    }

    public function getDescription(): ?string
    {
        return ' ';
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
    ];

    protected function getType(): string
    {
        return 'pie';
    }
}
