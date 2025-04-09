<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoDetalle extends Model
{
    protected $table = 'pago_detalles';

    protected $fillable = [
        'venta_id',
        'codigo_venta',
        'prod_asociados',
        'total_venta_bsd',
        'total_venta_usd',
        'efectivo_usd',
        'zelle_usd',
        'referencia_zelle_usd',
        'pagoMovil_bsd',
        'transferencia_bsd',
        'efectivo_bsd',
        'puntoVenta_bsd',
        'referencia_puntoVenta_bsd',
        'referencia_pagoMovil_bsd',
        'referencia_transferencia_bsd',
        'registrado_por',
    ];

    protected $casts = [
        'prod_asociados' => 'json'
    ];

    /**
     * Get the user that owns the PagoDetalle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'id', 'venta_id');
    }
}