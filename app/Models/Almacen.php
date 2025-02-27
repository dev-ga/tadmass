<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    //
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'correo',
        'responsable_almacen',
        'registrado_por'
        ];

    //belognsto tabla productos
    public function producto()    
    {
        return $this->belongsTo(Producto::class);
    }
}