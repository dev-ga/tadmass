<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PedidoDetalle extends Model
{
    //
    protected $table = 'pedido_detalles';

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio_venta'
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}