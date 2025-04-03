<?php

namespace App\Filament\Exports;

use App\Models\Pedido;
use OpenSpout\Common\Entity\Row;
use Filament\Actions\Exports\Exporter;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use Filament\Tables\Columns\Summarizers\Sum;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
// use OpenSpout\Common\Entity\Row;

class PedidoExporter extends Exporter
{
    protected static ?string $model = Pedido::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('codigo')
                ->label('Codigo'),
            ExportColumn::make('cliente.nombre')
                ->label('Cliente'),
            ExportColumn::make('vendedor.nombre')
                ->label('Vendedor'),
            ExportColumn::make('status')
                ->label('Estatus'),
            ExportColumn::make('monto_usd')
                ->label('Monto US$'),
            ExportColumn::make('monto_bsd')
                ->label('Monto VES(Bs.)'),
            ExportColumn::make('created_at')
                ->label('Fecha de Registro'),
            ExportColumn::make('registrado_por')
                ->label('Registrado Por'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your pedido export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    public function getXlsxHeaderCellStyle(): ?Style
    {
        return (new Style())
            ->setFontBold()
            ->setFontItalic()
            ->setFontSize(10)
            ->setFontName('Arial')
            ->setFontColor(Color::rgb(0,0,0))
            ->setBackgroundColor(Color::rgb(15, 192, 241))
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
            
    }

}