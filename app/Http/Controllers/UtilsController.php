<?php

namespace App\Http\Controllers;

use App\Models\PagoDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UtilsController extends Controller
{
    static function ingresosNetos() 
    {
        try {

            $ingresos_usd = PagoDetalle::sum('efectivo_usd');
            $ingresos_usd_zelle = PagoDetalle::sum('zelle_usd');
            $ingresos_usd_total = $ingresos_usd + $ingresos_usd_zelle;
            
            return $ingresos_usd_total;
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }
        
        
    }
}