<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventario extends Model
{
    //table
    protected $table = 'inventarios';

    //fillable
    protected $fillable = [
        'codigo',
        'almacen_id',
        'producto_id',
        'categoria_id',
        'existencia',
        'precio_venta_mayor',
        'precio_venta_detal',
        'registrado_por',
    ];

    /**
     * Get the producto that owns the Inventario
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(User::class, 'producto_id', 'id');
    }

    /**
     * Get all of the movimiento_inventarios for the Producto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function movimiento_inventarios(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'inventario_id', 'id');
    }
}