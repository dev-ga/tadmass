<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MovimientoInventarioController extends Controller
{
    static function mover_existencia($records, $data_formulario) {
        // dd($records, $data_formulario);
        try {
            DB::transaction(function () use ($records, $data_formulario) {

                //Codigo para crear movimiento de inventario
                $codigo = str_replace('-M-', '-D-',  $records[0]->codigo);

                //Consultamos si el producto ya existe en la tabla de productos para no duplicarlo
                $producto_info = DB::table('productos')->where('codigo', $codigo)->first();

                if (!$producto_info) {                   
                    $producto = new Producto();
                    $producto_a_mover = Producto::find($records[0]->producto_id);
                    $producto->codigo               = $codigo;
                    $producto->nombre               = $producto_a_mover->nombre;
                    $producto->descripcion          = $producto_a_mover->descripcion;
                    $producto->categoria_id         = $producto_a_mover->categoria_id;
                    $producto->imagen               = $producto_a_mover->imagen;
                    $producto->marca                = $producto_a_mover->marca;
                    $producto->modelo               = $producto_a_mover->modelo;
                    $producto->fecha_vencimiento    = $producto_a_mover->fecha_vencimiento;
                    $producto->unidad_medida        = 'unidad';
                    $producto->tipo_venta           = 'detal';
                    $producto->precio_venta         = $producto_a_mover->precio_venta;
                    $producto->status               = 1;
                    $producto->registrado_por       = Auth::user()->name;
                    $producto->save();

                    $inventario = new \App\Models\Inventario();
                    $inventario->existencia         = $data_formulario['cantidad'] * $producto_a_mover->cantidad_por_bulto;
                    $inventario->almacen_id         = $data_formulario['almacen_id'];
                    $inventario->producto_id        = $producto->id;
                    $inventario->codigo             = $producto->codigo;
                    $inventario->precio_venta       = $producto->precio_venta;
                    $inventario->registrado_por     = Auth::user()->name;
                    $inventario->save();

                    $movimiento_inventario = new \App\Models\MovimientoInventario();
                    $movimiento_inventario->inventario_id = $records[0]->id;
                    $movimiento_inventario->producto_id = $producto->id;
                    $movimiento_inventario->existencia = $data_formulario['cantidad'] * $producto_a_mover->cantidad_por_bulto;
                    $movimiento_inventario->tipo = 'entrada';
                    $movimiento_inventario->codigo_producto = $producto->codigo;
                    $movimiento_inventario->registrado_por = Auth::user()->name;
                    $movimiento_inventario->save();
                }


                
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}