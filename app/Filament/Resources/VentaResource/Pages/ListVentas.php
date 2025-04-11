<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVentas extends ListRecords
{
    protected static string $resource = VentaResource::class;

    protected static ?string $title = 'Gestion Administrativa: Ventas';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}