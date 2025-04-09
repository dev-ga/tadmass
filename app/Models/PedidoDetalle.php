<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoDetalle extends Model
{
    //
    protected $table = 'pedido_detalles';

    protected $fillable = [
        'pedido_id',
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_venta',
        'subtotal_venta'
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    //
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id', 'id');
    }
}