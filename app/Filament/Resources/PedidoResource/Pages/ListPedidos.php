<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPedidos extends ListRecords
{
    protected static string $resource = PedidoResource::class;

    protected static ?string $title = 'Gestion Administrativa: Pedidos';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}