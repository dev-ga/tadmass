<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Gasto;
use App\Models\Producto;
use App\Models\PagoDetalle;
use Illuminate\Http\Request;
use App\Models\Configuracion;
use App\Models\PedidoDetalle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UtilsController extends Controller
{
    static function ingresos()
    {
        try {

            $efectivo_usd   = PagoDetalle::select('efectivo_usd')->sum('efectivo_usd');
            $zelle_usd      = PagoDetalle::select('zelle_usd')->sum('zelle_usd');

            $efectivo_bsd   = PagoDetalle::select('efectivo_bsd')->sum('efectivo_bsd');
            $pm_bsd         = PagoDetalle::select('pagoMovil_bsd')->sum('pagoMovil_bsd');
            $trans_bsd      = PagoDetalle::select('transferencia_bsd')->sum('transferencia_bsd');
            
            $total_usd = $efectivo_usd + $zelle_usd;
            $total_bsd = $efectivo_bsd + $pm_bsd + $trans_bsd;

            /**
             * Conversion del total en bolivares VES a dolares US$
             */

            $tasa = Configuracion::first()->tasa_bcv;
            
            if(isset($tasa) && $tasa <= 0){
                throw new Exception("La tasa BCV no se encuentra configurada de forma correcta", 404);
                
            }else{
                $conversion_bsd_usd = $total_bsd / $tasa;
                
            }

            
            return $total_usd + $conversion_bsd_usd;
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }

    }

    static function egresos()
    {
        try {

            $usd   = Gasto::select('monto_usd')->sum('monto_usd');
            $bsd   = Gasto::select('monto_bsd')->sum('monto_bsd');

            /**
             * Conversion del total en bolivares VES a dolares US$
             */

            $tasa = Configuracion::first()->tasa_bcv;

            if (isset($tasa) && $tasa <= 0) {
                throw new Exception("La tasa BCV no se encuentra configurada de forma correcta", 404);
            } else {
                $conversion_bsd_usd = $bsd / $tasa;
            }

            return $usd + $conversion_bsd_usd;
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }
    }

    static function producto_max_venta()
    {
        try {

            $data = DB::table('pedido_detalles')
                ->select(DB::raw('producto_id as producto, SUM(cantidad) as cantidad'))
                ->groupBy('producto_id')
                ->get()
                ->toArray();

            $producto = Producto::select('nombre', 'id')->where('id', $data[0]->producto)->first();

            return $producto->nombre;
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }
    }
}