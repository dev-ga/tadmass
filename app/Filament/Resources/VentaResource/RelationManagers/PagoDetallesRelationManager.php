<?php

namespace App\Filament\Resources\VentaResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PagoDetalle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PagoDetallesRelationManager extends RelationManager
{
    protected static string $relationship = 'pagoDetalles';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('venta_id')
            ->columns([
            // Tables\Columns\TextColumn::make('prod_asociados')
            //     ->label('Productos Asociados')
            //     // ->getStateUsing(function (Venta $record) {
            //     //     $array = json_decode($record->prod_asociados);
            //     //     return $array;
            //     // })
            //     ->alignCenter()
            //     ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('tipo_pago')
                ->label('Metodo')
                ->badge(),
                Tables\Columns\TextColumn::make('efectivo_usd')
                ->label('Efectivo US$')
                ->money('USD'),
                Tables\Columns\TextColumn::make('zelle_usd')
                ->description(function ($record) {
                    return $record->referencia_zelle_usd ? '#REF: ' . $record->referencia_zelle_usd : '#REF:-----';
                })
                ->label('Zelle US$')
                ->money('USD'),
                Tables\Columns\TextColumn::make('efectivo_bsd')
                ->label('Efectivo VES(Bs.)')
                ->money('VES'),
                Tables\Columns\TextColumn::make('pagoMovil_bsd')
                ->description(function ($record) {
                    return $record->referencia_pagoMovil_bsd ? '#REF: ' . $record->referencia_pagoMovil_bsd : '#REF:-----';
                })
                ->label('Pago Movil VES(Bs.)')
                ->money('VES'),
                Tables\Columns\TextColumn::make('puntoVenta_bsd')
                ->description(function ($record) {
                    return $record->referencia_puntoVenta_bsd ? '#REF: ' . $record->referencia_puntoVenta_bsd : '#REF:-----';
                })
                ->label('Punto Venta VES(Bs.)')
                ->money('VES'),
                Tables\Columns\TextColumn::make('transferencia_bsd')
                ->description(function ($record) {
                    return $record->referencia_transferencia_bsd ? '#REF: ' . $record->referencia_transferencia_bsd : '#REF:-----';
                })
                ->label('Transferencia VES(Bs.)')
                ->money('VES'),
                Tables\Columns\TextColumn::make('total_venta_usd')
                ->label('Total Venta US$')
                ->money('USD'),
                Tables\Columns\TextColumn::make('total_venta_bsd')
                ->label('Total Venta VES(Bs.)')
                ->money('VES'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}