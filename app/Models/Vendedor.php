<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendedor extends Model
{
    protected $table = 'vendedors';

    //fillabel
    protected $fillable = [
        'nombre',
        'ci_rif',
        'email',
        'telefono',
        'direccion',
        'tipo',
        'registrado_por',
        'codigo'
    ];

    /**
     * Get all of the comments for the Vendedor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'vendedor_id', 'id');
    }
    
}