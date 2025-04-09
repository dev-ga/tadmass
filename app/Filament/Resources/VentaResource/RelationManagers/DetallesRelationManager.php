<?php

namespace App\Filament\Resources\VentaResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Venta;
use App\Models\Producto;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\VentaDetalle;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class DetallesRelationManager extends RelationManager
{
    protected static string $relationship = 'detalles';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('venta_id')
            ->columns([
                Tables\Columns\TextColumn::make('codido')
                    ->label('Codigo')
                    ->default(function (VentaDetalle $record) {
                        return Producto::find($record->producto_id)->codigo;
                    })
                    ->searchable()
                    ->badge()
                    ->color(function (VentaDetalle $record) {
                        $tipo_venta = Producto::find($record->producto_id)->tipo_venta;
                        if ($tipo_venta == 'detal') {
                            return 'verdeClaro';
                        }

                        if ($tipo_venta == 'mayor') {
                            return 'verdeOscuro';
                        }
                    }),
                Tables\Columns\TextColumn::make('producto.nombre')
                ->label('Producto')
                ->searchable(),
                Tables\Columns\TextColumn::make('precio_venta')
                ->label('Precio de Venta US$')
                ->money('USD'),
                Tables\Columns\TextColumn::make('cantidad')
                ->label('Cantidad')
                ->alignCenter()
                ->badge(),
                Tables\Columns\TextColumn::make('subtotal')
                ->label('Subtotal US$')
                ->money('USD')
                ->summarize(Sum::make()
                    ->money('USD')
                    ->label('Total de Venta($)'))
            ])
            ->filters([
                //
            ]);
    }
}