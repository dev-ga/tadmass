<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $table = 'pedidos';
    //
    protected $fillable = [
        'codigo',
        'cliente_id',
        'vendedor_id',
        'monto_usd',
        'monto_bsd',
        'productos',
        'registrado_por',
        'status'
    ];

    //cast
    protected $casts = [
        'tipo_usd' => 'array',
    ];

    /**
     * Get the user associated with the Pedido
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    /**
     * Get the user associated with the Pedido
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vendedor(): HasOne
    {
        return $this->hasOne(Vendedor::class, 'id', 'vendedor_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(PedidoDetalle::class);
    }

    /**
     * Get the user associated with the Pedido
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status(): HasOne
    {
        return $this->hasOne(Statu::class, 'id', 'status');
    }

}