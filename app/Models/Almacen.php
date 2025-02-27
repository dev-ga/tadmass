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
}