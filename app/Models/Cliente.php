<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    //
    protected $table = 'clientes';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'ci_rif',
        'email',
        'telefono',
        'direccion',
        'registrado_por',
    ];

    /**
     * Get all of the comments for the Vendedor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'cliente_id', 'id');
    }
    


}