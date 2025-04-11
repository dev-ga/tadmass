<?php

namespace App\Filament\Resources\VendedorResource\Pages;

use App\Filament\Resources\VendedorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendedors extends ListRecords
{
    protected static string $resource = VendedorResource::class;

    protected static ?string $title = 'Gestion de Vendedores';


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}