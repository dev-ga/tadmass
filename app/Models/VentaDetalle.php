<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    
    protected $table = 'venta_detalles';

    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_venta',
        'subtotal'
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function producto()
    {
        return $this->hasMany(Producto::class, 'id', 'producto_id');
    }

    
    
}