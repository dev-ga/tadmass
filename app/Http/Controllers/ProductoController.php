<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    static function json_productos($productos){
        // dd($productos);
        try {
            $array_productos = [];
            for ($i = 0; $i < count($productos); $i++) {
                $producto = Producto::select('nombre')->where('id', $productos[$i]['producto_id'])->first();
                array_push($array_productos, $producto);
            }
            return json_encode($array_productos);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    
}