<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MovimientoInventarioController extends Controller
{
    static function mover_existencia_masiva($record, $data_formulario) {

        try {
            //Codigo para crear movimiento de inventario
            $codigo = str_replace('-M-', '-D-',  $record->codigo);

            //Consultamos si el producto ya existe en la tabla de productos para no duplicarlo
            $producto_info = DB::table('productos')->where('codigo', $codigo)->first();

            if (!$producto_info) {

                DB::transaction(function () use ($record, $data_formulario, $codigo) {
                    $producto = new Producto();
                    $producto_a_mover = Producto::find($record->producto_id);
                    $producto->codigo               = $codigo;
                    $producto->nombre               = $producto_a_mover->nombre;
                    $producto->descripcion          = $producto_a_mover->descripcion;
                    $producto->categoria_id         = $producto_a_mover->categoria_id;
                    $producto->marca                = $producto_a_mover->marca;
                    $producto->modelo               = $producto_a_mover->modelo;
                    $producto->fecha_vencimiento    = $producto_a_mover->fecha_vencimiento;
                    $producto->unidad_medida        = 'unidad';
                    $producto->tipo_venta           = 'detal';
                    $producto->precio_venta         = $data_formulario['precio_venta'];
                    $producto->status               = 1;
                    $producto->registrado_por       = Auth::user()->name;
                    $producto->save();

                    $inventario = new \App\Models\Inventario();
                    $inventario->existencia         = $data_formulario['cantidad'] * $producto_a_mover->cantidad_por_bulto;
                    $inventario->almacen_id         = $data_formulario['almacen_id'];
                    $inventario->producto_id        = $producto->id;
                    $inventario->codigo             = $producto->codigo;
                    $inventario->precio_venta       = $producto->precio_venta;
                    $inventario->unidad_medida      = 'unidad';
                    $inventario->registrado_por     = Auth::user()->name;
                    $inventario->save();

                    //Salida de inventario
                    $movimiento_inventario = new \App\Models\MovimientoInventario();
                    $movimiento_inventario->inventario_id   = $record->id;
                    $movimiento_inventario->producto_id     = $record->producto_id;
                    $movimiento_inventario->codigo_producto = $record->codigo;
                    $movimiento_inventario->cantidad        = $data_formulario['cantidad'];
                    $movimiento_inventario->tipo            = 'salida';
                    $movimiento_inventario->unidad_medida   = $record->unidad_medida;
                    $movimiento_inventario->registrado_por  = Auth::user()->name;
                    $movimiento_inventario->save();

                    //Resto de la existencia de inventario la cantidad que se va a mover
                    $record->existencia = $record->existencia - $data_formulario['cantidad'];
                    $record->save();

                    //Entrada de inventario
                    $movimiento_inventario = new \App\Models\MovimientoInventario();
                    $movimiento_inventario->inventario_id   = $inventario->id;
                    $movimiento_inventario->producto_id     = $producto->id;
                    $movimiento_inventario->cantidad        = $data_formulario['cantidad'] * $producto_a_mover->cantidad_por_bulto;
                    $movimiento_inventario->unidad_medida   = 'unidad';
                    $movimiento_inventario->tipo            = 'entrada';
                    $movimiento_inventario->codigo_producto = $producto->codigo;
                    $movimiento_inventario->registrado_por  = 'sistema';
                    $movimiento_inventario->save();
                });

                return [
                    'success' => true,
                    'message' => "El producto se ha movido con exito",
                ];
                
            }else{
                
                return [
                    'success' => false,
                    'message' => "El producto ya existe en el almacen seleccionado, debe usar la accion de reposicion de inventario para actualizar la cantidad de producto en el almacen.",
                ];
            }
                
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }
    }

    static function mover_existencia_individual($record, $data_formulario)
    {

        try {
            //Codigo para crear movimiento de inventario
            $codigo = str_replace('-M-', '-D-',  $record->codigo);

            //Consultamos si el producto ya existe en la tabla de productos para no duplicarlo
            $producto_info = DB::table('productos')->where('codigo', $codigo)->first();

            $producto_a_mover = Producto::find($record->producto_id);

            if (!$producto_info) {

                DB::transaction(function () use ($record, $data_formulario, $codigo, $producto_a_mover) {
                    
                    $producto = new Producto();
                    $producto->codigo               = $codigo;
                    $producto->nombre               = $producto_a_mover->nombre;
                    $producto->descripcion          = $producto_a_mover->descripcion;
                    $producto->categoria_id         = $producto_a_mover->categoria_id;
                    $producto->marca                = $producto_a_mover->marca;
                    $producto->modelo               = $producto_a_mover->modelo;
                    $producto->fecha_vencimiento    = $producto_a_mover->fecha_vencimiento;
                    $producto->unidad_medida        = 'unidad';
                    $producto->tipo_venta           = 'detal';
                    $producto->precio_venta         = $data_formulario['precio_venta'];
                    $producto->status               = 1;
                    $producto->registrado_por       = Auth::user()->name;
                    $producto->save();

                    $inventario = new \App\Models\Inventario();
                    $inventario->existencia         = $data_formulario['cantidad'] * $producto_a_mover->cantidad_por_bulto;
                    $inventario->almacen_id         = $data_formulario['almacen_id'];
                    $inventario->producto_id        = $producto->id;
                    $inventario->codigo             = $producto->codigo;
                    $inventario->precio_venta       = $producto->precio_venta;
                    $inventario->unidad_medida      = 'unidad';
                    $inventario->registrado_por     = Auth::user()->name;
                    $inventario->save();

                    //Salida de inventario
                    $movimiento_inventario = new \App\Models\MovimientoInventario();
                    $movimiento_inventario->inventario_id   = $record->id;
                    $movimiento_inventario->producto_id     = $record->producto_id;
                    $movimiento_inventario->codigo_producto = $record->codigo;
                    $movimiento_inventario->cantidad        = $data_formulario['cantidad'];
                    $movimiento_inventario->tipo            = 'salida';
                    $movimiento_inventario->unidad_medida   = $record->unidad_medida;
                    $movimiento_inventario->registrado_por  = Auth::user()->name;
                    $movimiento_inventario->save();

                    //Resto de la existencia de inventario la cantidad que se va a mover
                    $record->existencia = $record->existencia - $data_formulario['cantidad'];
                    $record->save();

                    //Entrada de inventario
                    $movimiento_inventario = new \App\Models\MovimientoInventario();
                    $movimiento_inventario->inventario_id   = $inventario->id;
                    $movimiento_inventario->producto_id     = $producto->id;
                    $movimiento_inventario->cantidad        = $data_formulario['cantidad'] * $producto_a_mover->cantidad_por_bulto;
                    $movimiento_inventario->unidad_medida   = 'unidad';
                    $movimiento_inventario->tipo            = 'entrada';
                    $movimiento_inventario->codigo_producto = $producto->codigo;
                    $movimiento_inventario->registrado_por  = 'sistema';
                    $movimiento_inventario->save();
                });

                return [
                    'success' => true,
                    'message' => "El producto se ha movido con exito",
                ];
                
            } else {

                DB::transaction(function () use ($record, $data_formulario, $producto_a_mover) {


                    $record->existencia = $record->existencia + $data_formulario['cantidad'] * $producto_a_mover->cantidad_por_bulto;
                    $record->save();

                    //Salida de inventario
                    $movimiento_inventario = new \App\Models\MovimientoInventario();
                    $movimiento_inventario->inventario_id   = $record->id;
                    $movimiento_inventario->producto_id     = $record->producto_id;
                    $movimiento_inventario->codigo_producto = $record->codigo;
                    $movimiento_inventario->cantidad        = $data_formulario['cantidad'];
                    $movimiento_inventario->tipo            = 'reposicion';
                    $movimiento_inventario->unidad_medida   = $record->unidad_medida;
                    $movimiento_inventario->registrado_por  = Auth::user()->name;
                    $movimiento_inventario->save();

                });

                return [
                    'success' => true,
                    'message' => "La reposicion del producto se ha realizado con exito",
                ];
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }
    }
}