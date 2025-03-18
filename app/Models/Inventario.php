<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
     * Get the producto associated with the Inventario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function producto(): HasOne
    {
        return $this->hasOne(Producto::class, 'id', 'producto_id');
    }

    /**
     * Get all of the movimiento_inventarios for the Producto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function movimientoInventarios(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'inventario_id', 'id');
    }

    /**
     * Get the user associated with the Inventario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function almacen(): HasOne
    {
        return $this->hasOne(Almacen::class, 'id', 'almacen_id');
    }

    /**
     * Get the user associated with the Inventario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function categoria(): HasOne
    {
        return $this->hasOne(Categoria::class, 'id', 'almacen_id');
    }
}