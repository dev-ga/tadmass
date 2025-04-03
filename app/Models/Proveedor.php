<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proveedor extends Model
{
    protected $table = 'proveedors';

    //fillable
    protected $fillable = [
        'codigo',
        'ci_rif',
        'nombre',
        'telefono',
        'email',
        'direccion',
        'registrado_por',
    ];

    /**
     * Get the gastos that owns the Proveedor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }
}