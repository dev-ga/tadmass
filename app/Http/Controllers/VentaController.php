<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use App\Models\Configuracion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    
    static function registrar_venta_usd($record, $data_formulario , array $detalles) {
        // dd($record, $data_formulario ,$detalles);
        try {

            //Calculo de las comisiones venta segun el detalle
            $comisiones = [];

            for ($i = 0; $i < count($detalles); $i++) {
                
                $producto = Producto::find($detalles[$i]['producto_id']);
                $porcentaje = ($producto->categoria->comision * $producto->precio_venta) / 100;
                $total_comision = $porcentaje * $detalles[$i]['cantidad'];

                array_push($comisiones, $total_comision);
                
            }

            $comisiones_venta = array_sum($comisiones);
            
            DB::transaction(function () use ($record, $data_formulario, $comisiones_venta, $detalles) {

                /**
                 * Logica para seleccionar el tipo de pago
                 * 
                 * @param $tipo_pago
                 * @return $tipo_pago
                 */
                //-----------------------------------------------------------------------------
                if($data_formulario['cash'] != null && $data_formulario['zelle'] == null) {
                    $tipo_pago = 'cash';
                } else if($data_formulario['cash'] == 0 && $data_formulario['zelle'] > 0) {
                    $tipo_pago = 'zelle';
                } else if ($data_formulario['cash'] > 0 && $data_formulario['zelle'] > 0) {
                    $tipo_pago = 'multiple-usd';
                }
                else {
                    $tipo_pago = 'cash';
                }
                //-------------------------------------------------------------------------------
                

                $venta = new Venta();
                $venta->codigo            = 'TADMASS-V-' . rand(111111, 999999);
                $venta->cliente_id        = $record->cliente_id;
                $venta->vendedor_id       = $record->vendedor_id;
                $venta->metodo_pago       = $data_formulario['metodo_pago'];
                $venta->tipo_pago_usd     = $tipo_pago;
                $venta->referencia_usd    = isset($data_formulario['ref_zelle']) ? $data_formulario['ref_zelle'] : 'N/A';
                $venta->cash              = $data_formulario['cash']  == null ? $data_formulario['total_usd'] : $data_formulario['cash'];
                $venta->zelle             = $data_formulario['zelle'] == null ? 0.00 : $data_formulario['zelle'];
                $venta->total_venta_usd   = $data_formulario['total_usd'];
                $venta->total_venta_bsd   = $data_formulario['total_bsd'];
                $venta->comision_usd      = $comisiones_venta;
                $venta->tasa_bcv          = Configuracion::first()->tasa_bcv;
                $venta->registrado_por    = Auth::user()->name;
                $venta->save();

                for ($i = 0; $i < count($detalles); $i++) {
                    $detalle = new VentaDetalle();
                    $detalle->venta_id = $venta->id;
                    $detalle->producto_id = $detalles[$i]['producto_id'];
                    $detalle->cantidad = $detalles[$i]['cantidad'];
                    $detalle->precio_venta = $detalles[$i]['precio_venta'];
                    $detalle->save();
                }
                
            });
            
            return [
                'success' => true,
                'message' => 'Venta registrada exitosamente',
            ];
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }
        
    }

    static function registrar_venta_bsd($record, $data_formulario, array $detalles)
    {

        try {

            //Calculo de las comisiones venta segun el detalle
            $comisiones = [];

            for ($i = 0; $i < count($detalles); $i++) {

                $producto = Producto::find($detalles[$i]['producto_id']);
                $porcentaje = ($producto->categoria->comision * $producto->precio_venta) / 100;
                $total_comision = $porcentaje * $detalles[$i]['cantidad'];

                array_push($comisiones, $total_comision);
            }

            $comisiones_venta = array_sum($comisiones);

            DB::transaction(function () use ($record, $data_formulario, $comisiones_venta, $detalles) {

                $venta = new Venta();
                $venta->codigo            = 'TADMASS-V-' . rand(111111, 999999);
                $venta->cliente_id        = $record->cliente_id;
                $venta->vendedor_id       = $record->vendedor_id;
                $venta->metodo_pago       = $data_formulario['metodo_pago'];
                $venta->tipo_pago_bsd     = $data_formulario['tipo_bsd'];
                $venta->monto_bsd         = $data_formulario['total_bsd'];
                $venta->referencia_bsd    = $data_formulario['ref_bsd']  != null ? $data_formulario['ref_bsd'] : 'N/A';
                $venta->total_venta_usd   = $data_formulario['total_usd'];
                $venta->total_venta_bsd   = $data_formulario['total_bsd'];
                $venta->comision_usd      = $comisiones_venta;
                $venta->tasa_bcv          = Configuracion::first()->tasa_bcv;
                $venta->registrado_por    = Auth::user()->name;
                $venta->save();

                for ($i = 0; $i < count($detalles); $i++) {
                    $detalle = new VentaDetalle();
                    $detalle->venta_id = $venta->id;
                    $detalle->producto_id = $detalles[$i]['producto_id'];
                    $detalle->cantidad = $detalles[$i]['cantidad'];
                    $detalle->precio_venta = $detalles[$i]['precio_venta'];
                    $detalle->save();
                }
            });

            return [
                'success' => true,
                'message' => 'Venta registrada exitosamente',
            ];
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }
    }

    static function registrar_venta_multiple($record, $data_formulario, array $detalles)
    {
// dd($record, $data_formulario, $detalles);
        try {

            //Calculo de las comisiones venta segun el detalle
            $comisiones = [];

            for ($i = 0; $i < count($detalles); $i++) {

                $producto = Producto::find($detalles[$i]['producto_id']);
                $porcentaje = ($producto->categoria->comision * $producto->precio_venta) / 100;
                $total_comision = $porcentaje * $detalles[$i]['cantidad'];

                array_push($comisiones, $total_comision);
            }

            $comisiones_venta = array_sum($comisiones);

            DB::transaction(function () use ($record, $data_formulario, $comisiones_venta, $detalles) {

                $venta = new Venta();
                $venta->codigo            = 'TADMASS-V-' . rand(111111, 999999);
                $venta->cliente_id        = $record->cliente_id;
                $venta->vendedor_id       = $record->vendedor_id;
                $venta->metodo_pago       = $data_formulario['metodo_pago'];
                $venta->tipo_pago_bsd     = $data_formulario['tipo_bsd'];
                $venta->tipo_pago_usd     = $data_formulario['tipo_usd'];
                $venta->cash              = $data_formulario['tipo_usd'] == 'cash' ? $data_formulario['pago_usd'] : 0.00;
                $venta->zelle             = $data_formulario['tipo_usd'] == 'zelle' ? $data_formulario['pago_usd'] : 0.00;
                $venta->monto_bsd         = $data_formulario['pago_bsd'];
                $venta->referencia_usd    = isset($data_formulario['multiple_ref_usd']) ? $data_formulario['multiple_ref_usd'] : 'N/A';
                $venta->referencia_bsd    = isset($data_formulario['multiple_ref_bsd']) ? $data_formulario['multiple_ref_bsd'] : 'N/A';
                $venta->total_venta_usd   = $data_formulario['total_usd'];
                $venta->total_venta_bsd   = $data_formulario['total_bsd'];
                $venta->comision_usd      = $comisiones_venta;
                $venta->tasa_bcv          = Configuracion::first()->tasa_bcv;
                $venta->registrado_por    = Auth::user()->name;
                $venta->save();

                for ($i = 0; $i < count($detalles); $i++) {
                    $detalle = new VentaDetalle();
                    $detalle->venta_id = $venta->id;
                    $detalle->producto_id = $detalles[$i]['producto_id'];
                    $detalle->cantidad = $detalles[$i]['cantidad'];
                    $detalle->precio_venta = $detalles[$i]['precio_venta'];
                    $detalle->save();
                }
                
            });

            return [
                'success' => true,
                'message' => 'Venta registrada exitosamente',
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }
    }
}