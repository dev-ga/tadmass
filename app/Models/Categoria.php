<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    //
    protected $table = 'categorias';
    
    protected $fillable = [
        'nombre',
        'slug',
        'registrado_por'
    ];

    /**
     * Get all of the comments for the Categoria
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id', 'id');
    }
}