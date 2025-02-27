<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proveedor extends Model
{
    protected $table = 'proveedors';

    //fillable
    protected $fillable = [
        'id',
        'nombre',
        'telefono',
        'correo',
        'direccion',
        'registrado_por',
    ];

    /**
     * Get the gsto that owns the Proveedor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gasto(): BelongsTo
    {
        return $this->belongsTo(Gasto::class, 'proveedor_id', 'id');
    }
}