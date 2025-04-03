<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodo_pagos';
    protected $fillable = ['tipo_moneda','descripcion', 'usa_referencia'];
    
}