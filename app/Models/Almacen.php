<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    //
    protected $fillable = [
        'codigo',
        'nombre',
        'direccion',
        'telefono',
        'correo',
        'responsable_almacen',
        'registrado_por',
        'tipo_almacen'
        
    ];

    //belognsto tabla productos
    public function producto()    
    {
        return $this->belongsTo(Producto::class);
    }
}