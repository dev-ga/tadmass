<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venta extends Model
{
    //table
    protected $table = 'ventas';

    //fillable
    protected $fillable = [
        'codigo',
        'cliente_id',
        'vendedor_id',
        'fecha',
        'metodo_pago',
        'monto_usd',
        'monto_bsd',
        'totaL_venta',
        'tasa_bcv',
        'comision_usd',
        'comision_bsd',
        'registrado_por',
    ];

    /**
     * Get the cliente that owns the Venta
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id');
    }

    /**
     * Get the vendedor that owns the Venta
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(Vendedor::class, 'vendedor_id', 'id');
    }
}