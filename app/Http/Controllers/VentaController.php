<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\PagoDetalle;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use App\Models\Configuracion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Http\Controllers\ProductoController;

class VentaController extends Controller
{
    
    static function registrar_venta_usd($record, $data_formulario , array $detalles) {

        try {

            //Calculo de las comisiones de venta segun el detalle y array de productos
            $comisiones = [];
            $array_productos = [];

            for ($i = 0; $i < count($detalles); $i++) {
                
                $producto = Producto::find($detalles[$i]['producto_id']);
                $porcentaje = ($producto->categoria->comision * $producto->precio_venta) / 100;
                $total_comision = $porcentaje * $detalles[$i]['cantidad'];

                array_push($comisiones, $total_comision);
                array_push($array_productos, $producto->nombre);
                
            }

            $comisiones_venta = array_sum($comisiones);
            
            DB::transaction(function () use ($record, $data_formulario, $comisiones_venta, $detalles, $array_productos) {

                $venta = new Venta();
                $venta->codigo            = 'TADMASS-V-' . rand(111111, 999999);
                $venta->cliente_id        = $record->cliente_id;
                $venta->vendedor_id       = $record->vendedor_id;
                $venta->metodo_pago       = $data_formulario['metodo_pago'];
                $venta->total_venta_usd   = $data_formulario['total_usd'];
                $venta->total_venta_bsd   = $data_formulario['total_bsd'];
                $venta->tasa_bcv          = Configuracion::first()->tasa_bcv;
                $venta->comision_usd      = $comisiones_venta;
                $venta->comision_bsd      = $comisiones_venta * $venta->tasa_bcv;
                $venta->registrado_por    = Auth::user()->name;
                $venta->prod_asociados    = $array_productos;
                $venta->save();

                //Actualizamos el estatu del pedido
                $record->status = 'procesado';
                $record->save();
                
                for ($i = 0; $i < count($detalles); $i++) {
                    $detalle = new VentaDetalle();
                    $detalle->venta_id = $venta->id;
                    $detalle->producto_id = $detalles[$i]['producto_id'];
                    $detalle->cantidad = $detalles[$i]['cantidad'];
                    $detalle->precio_venta = $detalles[$i]['precio_venta'];
                    $detalle->subtotal = $detalle->precio_venta * $detalle->cantidad;
                    $detalle->save();

                }

                /**
                 * Logica para registrar el detalle del pago en la tabla de pago_detalles
                 */
                //-------------------------------------------------------------------------------
                //Efectivo US$
                $pago = new PagoDetalle();
                if($data_formulario['cash'] != null && $data_formulario['zelle'] == null) {
                    $pago->venta_id = $venta->id;
                    $pago->codigo_venta = $venta->codigo;
                    $pago->tipo_pago = 'efectivo US$';
                    $pago->prod_asociados    = $array_productos;
                    $pago->total_venta_usd   = $data_formulario['total_usd'];
                    $pago->total_venta_bsd   = $data_formulario['total_bsd'];
                    $pago->efectivo_usd      = $data_formulario['cash'];
                    $pago->referencia_zelle_usd = $data_formulario['ref_zelle'] ? $data_formulario['ref_zelle'] : 'N/A';
                    $pago->registrado_por    = Auth::user()->name;
                    $pago->save();

                //Zelle US$
                } else if($data_formulario['cash'] == 0 && $data_formulario['zelle'] > 0) {
                    $pago->venta_id             = $venta->id;
                    $pago->codigo_venta         = $venta->codigo;
                    $pago->tipo_pago            = 'zelle US$';
                    $pago->prod_asociados    = $array_productos;
                    $pago->total_venta_usd      = $data_formulario['total_usd'];
                    $pago->total_venta_bsd      = $data_formulario['total_bsd'];
                    $pago->zelle_usd            = $data_formulario['zelle'];
                    $pago->referencia_zelle_usd = $data_formulario['ref_zelle'];
                    $pago->registrado_por       = Auth::user()->name;
                    $pago->save();

                //Multiple US$
                } else if ($data_formulario['cash'] > 0 && $data_formulario['zelle'] > 0) {
                    $pago->venta_id             = $venta->id;
                    $pago->codigo_venta         = $venta->codigo;
                    $pago->tipo_pago            = 'multiple US$';
                    $pago->prod_asociados    = $array_productos;
                    $pago->total_venta_usd      = $data_formulario['total_usd'];
                    $pago->total_venta_bsd      = $data_formulario['total_bsd'];
                    $pago->efectivo_usd         = $data_formulario['cash'];
                    $pago->zelle_usd            = $data_formulario['zelle'];
                    $pago->referencia_zelle_usd = $data_formulario['ref_zelle'];
                    $pago->registrado_por       = Auth::user()->name;
                    $pago->save();
                }
                
            });
            
            return [
                'success' => true,
                'message' => 'Venta registrada exitosamente',
            ];
            
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
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
            $array_productos = [];

            for ($i = 0; $i < count($detalles); $i++) {

                $producto = Producto::find($detalles[$i]['producto_id']);
                $porcentaje = ($producto->categoria->comision * $producto->precio_venta) / 100;
                $total_comision = $porcentaje * $detalles[$i]['cantidad'];

                array_push($comisiones, $total_comision);
                array_push($array_productos, $producto->nombre);
            }

            $comisiones_venta = array_sum($comisiones);

            DB::transaction(function () use ($record, $data_formulario, $comisiones_venta, $detalles, $array_productos) {

                $venta = new Venta();
                $venta->codigo            = 'TADMASS-V-' . rand(111111, 999999);
                $venta->cliente_id        = $record->cliente_id;
                $venta->vendedor_id       = $record->vendedor_id;
                $venta->metodo_pago       = $data_formulario['metodo_pago'];
                $venta->total_venta_usd   = $data_formulario['total_usd'];
                $venta->total_venta_bsd   = $data_formulario['total_bsd'];
                $venta->tasa_bcv          = Configuracion::first()->tasa_bcv;
                $venta->comision_usd      = $comisiones_venta;
                $venta->comision_bsd      = $comisiones_venta * $venta->tasa_bcv;
                $venta->registrado_por    = Auth::user()->name;
                $venta->prod_asociados    = $array_productos;
                $venta->save();

                //Actualizamos el estatu del pedido
                $record->status = 'procesado';
                $record->save();

                for ($i = 0; $i < count($detalles); $i++) {
                    $detalle = new VentaDetalle();
                    $detalle->venta_id = $venta->id;
                    $detalle->producto_id = $detalles[$i]['producto_id'];
                    $detalle->cantidad = $detalles[$i]['cantidad'];
                    $detalle->precio_venta = $detalles[$i]['precio_venta'];
                    $detalle->subtotal = $detalle->precio_venta * $detalle->cantidad;
                    $detalle->save();
                }

                /**
                 * Logica para registrar el detalle del pago en la tabla de pago_detalles
                 */
                //-------------------------------------------------------------------------------
                $pago = new PagoDetalle();
                
                /**
                 * Inicializamos los pagos y las referencias en variables ya que no sabemos como va a pagar el cliente
                 * De igual manera la suma de los montos introducidos por el usuario debe ser igual
                 * al monto total de la venta
                 */
                $pagoMovil_bsd      = isset($data_formulario['pagoMovil_bsd']) ? $data_formulario['pagoMovil_bsd'] : 0;
                $puntoVenta_bsd     = isset($data_formulario['puntoVenta_bsd']) ? $data_formulario['puntoVenta_bsd'] : 0;
                $transferencia_bsd  = isset($data_formulario['transferencia_bsd']) ? $data_formulario['transferencia_bsd'] : 0;

                $referencia_pagoMovil_bsd      = isset($data_formulario['referencia_pagoMovil_bsd'])  ? $data_formulario['referencia_pagoMovil_bsd'] : 'N/A';
                $referencia_puntoVenta_bsd     = isset($data_formulario['referencia_puntoVenta_bsd'])  ? $data_formulario['referencia_puntoVenta_bsd'] : 'N/A';
                $referencia_transferencia_bsd  = isset($data_formulario['referencia_transferencia_bsd'])  ? $data_formulario['referencia_transferencia_bsd'] : 'N/A';

                $total_bsd = $pagoMovil_bsd + $puntoVenta_bsd + $transferencia_bsd;

                
                if(count($data_formulario['tipo_bsd']) > 1)
                {
                    $tipo_pago = 'multiple VES(Bs.)';
                }

                if (count($data_formulario['tipo_bsd']) == 1) {
                    $tipo_pago = $data_formulario['tipo_bsd'][0];
                }

                if ($total_bsd != $data_formulario['total_bsd']) {
                    throw new Exception('El monto total de la venta no coincide con la suma de los pagos', 400);
                    
                }else{
                    $pago->venta_id = $venta->id;
                    $pago->codigo_venta = $venta->codigo;
                    $pago->tipo_pago = $tipo_pago;
                    $pago->prod_asociados    = $array_productos;
                    $pago->total_venta_usd   = $data_formulario['total_usd'];
                    $pago->total_venta_bsd   = $data_formulario['total_bsd'];
                    $pago->pagoMovil_bsd      = $pagoMovil_bsd;
                    $pago->puntoVenta_bsd     = $puntoVenta_bsd;
                    $pago->transferencia_bsd  = $transferencia_bsd;
                    $pago->referencia_pagoMovil_bsd      = $referencia_pagoMovil_bsd;
                    $pago->referencia_puntoVenta_bsd     = $referencia_puntoVenta_bsd;
                    $pago->referencia_transferencia_bsd  = $referencia_transferencia_bsd;
                    
                    $pago->registrado_por    = Auth::user()->name;
                    $pago->save();
                }
                
            });

            return [
                'success' => true,
                'message' => 'Venta registrada exitosamente',
            ];
            
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return [
                'success' => false,
                'message' => $th->getMessage(),
            ];
        }
    }

    static function registrar_venta_multiple($record, $data_formulario, array $detalles)
    {

        try {

            //Calculo de las comisiones venta segun el detalle
            $comisiones = [];
            $array_productos = [];

            for ($i = 0; $i < count($detalles); $i++) {

                $producto = Producto::find($detalles[$i]['producto_id']);
                $porcentaje = ($producto->categoria->comision * $producto->precio_venta) / 100;
                $total_comision = $porcentaje * $detalles[$i]['cantidad'];

                array_push($comisiones, $total_comision);
                array_push($array_productos, $producto->nombre);
            }

            $comisiones_venta = array_sum($comisiones);

            DB::transaction(function () use ($record, $data_formulario, $comisiones_venta, $detalles, $array_productos) {

                $venta = new Venta();
                $venta->codigo            = 'TADMASS-V-' . rand(111111, 999999);
                $venta->cliente_id        = $record->cliente_id;
                $venta->vendedor_id       = $record->vendedor_id;
                $venta->metodo_pago       = $data_formulario['metodo_pago'];
                $venta->total_venta_usd   = $data_formulario['total_usd'];
                $venta->total_venta_bsd   = $data_formulario['total_bsd'];
                $venta->tasa_bcv          = Configuracion::first()->tasa_bcv;
                $venta->comision_usd      = $comisiones_venta;
                $venta->comision_bsd      = $comisiones_venta * $venta->tasa_bcv;
                $venta->registrado_por    = Auth::user()->name;
                $venta->prod_asociados    = $array_productos;
                $venta->save();

                //Actualizamos el estatu del pedido
                $record->status = 'procesado';
                $record->save();

                for ($i = 0; $i < count($detalles); $i++) {
                    $detalle = new VentaDetalle();
                    $detalle->venta_id = $venta->id;
                    $detalle->producto_id = $detalles[$i]['producto_id'];
                    $detalle->cantidad = $detalles[$i]['cantidad'];
                    $detalle->precio_venta = $detalles[$i]['precio_venta'];
                    $detalle->subtotal = $detalle->precio_venta * $detalle->cantidad;
                    $detalle->save();
                }

                if (count($data_formulario['tipo_bsd']) > 1) {
                    throw new Exception('Solo debe seleccionar un metodo de pago en bolibares VES(Bs.), favor vuelva a intentar', 400);
                }

                $pago = new PagoDetalle();
                $pago->venta_id                         = $venta->id;
                $pago->codigo_venta                     = $venta->codigo;
                $pago->tipo_pago                        = 'multiple US$-VES(Bs.)';
                $pago->prod_asociados                   = $array_productos;
                $pago->total_venta_usd                  = $data_formulario['total_usd'];
                $pago->total_venta_bsd                  = $data_formulario['total_bsd'];
                $pago->efectivo_usd                     = $data_formulario['tipo_usd'] == 'cash' ? $data_formulario['pago_usd'] : 0.00;
                $pago->zelle_usd                        = $data_formulario['tipo_usd'] == 'zelle' ? $data_formulario['pago_usd'] : 0.00;
                $pago->referencia_zelle_usd             = isset($data_formulario['ref_zelle'])  ? $data_formulario['ref_zelle'] : 'N/A';
                $pago->pagoMovil_bsd                    = $data_formulario['tipo_bsd'][0] == 'pago-movil' ? $data_formulario['pago_bsd'] : 0.00;
                $pago->puntoVenta_bsd                   = $data_formulario['tipo_bsd'][0] == 'punto' ? $data_formulario['pago_bsd'] : 0.00;
                $pago->transferencia_bsd                = $data_formulario['tipo_bsd'][0] == 'transferencia' ? $data_formulario['pago_bsd'] : 0.00;
                $pago->referencia_pagoMovil_bsd         = isset($data_formulario['referencia_pagoMovil_bsd'])  ? $data_formulario['referencia_pagoMovil_bsd'] : 'N/A';
                $pago->referencia_puntoVenta_bsd        = isset($data_formulario['referencia_puntoVenta_bsd'])  ? $data_formulario['referencia_puntoVenta_bsd'] : 'N/A';
                $pago->referencia_transferencia_bsd     = isset($data_formulario['referencia_transferencia_bsd'])  ? $data_formulario['referencia_transferencia_bsd'] : 'N/A';
                $pago->registrado_por                   = Auth::user()->name;
                $pago->save();
                
            });

            return [
                'success' => true,
                'message' => 'Venta registrada exitosamente',
            ];
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