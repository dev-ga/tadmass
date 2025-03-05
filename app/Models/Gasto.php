<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    //table
    protected $table = 'gastos';

    //fillable
    protected $fillable = [
        // 'nro_gasto',
        'codigo',
        'nro_control',
        'fecha_factura',
        'descripcion',
        'proveedor_id',
        'numero_factura',
        'fecha',
        'metodo_pago',
        'tasa_bcv',
        'monto_usd',
        'monto_bsd',
        'iva',
        'total_gasto_bsd',
        'conversion_usd',
        'registrado_por',
        'observacion'
    ];

    /**
     * Get all of the proveedores for the Compra
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }
}