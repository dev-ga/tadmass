<?php

namespace App\Filament\Resources\ProductoResource\Pages;

use Filament\Actions;
use App\Models\Almacen;
use App\Models\Inventario;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProductoResource;

class CreateProducto extends CreateRecord
{
    protected static string $resource = ProductoResource::class;
    
    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\ViewAction::make(),
    //         Actions\CreateAction::make(),
    //     ];
    // }
    
    //aftercreate
    protected function afterCreate(): void
    {
        try {

            $registro_inventario = new Inventario();
            
            if($this->data['tipo_venta'] == 'mayor'){
                $registro_inventario->existencia = $this->data['existencia'];
                $registro_inventario->almacen_id = Almacen::where('tipo_almacen', $this->data['tipo_venta'])->first()->id;
                $registro_inventario->producto_id = $this->record->id;
                $registro_inventario->codigo = $this->record->codigo;
                $registro_inventario->precio_venta = $this->data['precio_venta'];
                $registro_inventario->registrado_por = Auth::user()->name;
                $registro_inventario->save();

                //Creamos registro en movimiento_inventarios
                $movimiento_inventario = new \App\Models\MovimientoInventario();
                $movimiento_inventario->inventario_id = $registro_inventario->id;
                $movimiento_inventario->producto_id = $this->record->id;
                $movimiento_inventario->cantidad = $this->data['existencia'];
                $movimiento_inventario->tipo = 'entrada';
                $movimiento_inventario->codigo_producto = $this->record->codigo;
                $movimiento_inventario->registrado_por = Auth::user()->name;
                $movimiento_inventario->save();
                
            }

            if ($this->data['tipo_venta'] == 'detal') {
                $registro_inventario->existencia = $this->data['existencia'];
                $registro_inventario->almacen_id = Almacen::where('tipo_almacen', $this->data['tipo_venta'])->first()->id;
                $registro_inventario->producto_id = $this->record->id;
                $registro_inventario->codigo = $this->record->codigo;
                $registro_inventario->precio_venta = $this->data['precio_venta'];
                $registro_inventario->registrado_por = Auth::user()->name;
                $registro_inventario->save();

                //Creamos registro en movimiento_inventarios
                $movimiento_inventario = new \App\Models\MovimientoInventario();
                $movimiento_inventario->inventario_id = $registro_inventario->id;
                $movimiento_inventario->producto_id = $this->record->id;
                $movimiento_inventario->cantidad = $this->data['existencia'];
                $movimiento_inventario->tipo = 'entrada';
                $movimiento_inventario->codigo_producto = $this->record->codigo;
                $movimiento_inventario->registrado_por = Auth::user()->name;
                $movimiento_inventario->save();
            }  

            //code...
        } catch (\Throwable $th) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body($th->getMessage())
                ->send();
        }
        
        
        // $this->redirect(static::getRedirectUrl());
    }
    
    
}