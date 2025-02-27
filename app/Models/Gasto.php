<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gasto extends Model
{
    //table
    protected $table = 'gastos';

    //fillable
    protected $fillable = [
        'nro_gasto',
        'codigo',
        'descripcion',
        'tipo_gasto',
        'numero_factura',
        'nro_control',
        'fecha',
        'fecha_factura',
        'proveedor_id',
        'metodo_pago',
        'tasa_bcv',
        'monto_usd',
        'monto_bsd',
        'iva',
        'total_gasto_bsd',
        'conversion_usd',
        'registrado_por',
    ];

    /**
     * Get all of the proveedores for the Gasto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proveedores(): HasMany
    {
        return $this->hasMany(Proveedor::class, 'proveedor_id', 'id');
    }
}