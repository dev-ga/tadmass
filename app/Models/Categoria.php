<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //
    protected $table = 'categorias';
    
    protected $fillable = [
        'nombre',
        'slug',
        'registrado_por'
    ];

    //Relacion UNO a UNO con l a tabla productos
    public function producto()
    {
        return $this->hasOne(Producto::class);
    }
}