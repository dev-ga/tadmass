<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    //
    protected $table = 'productos';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria_id',
        'imagen',
        'marca',
        'modelo',
        'fecha_vencimiento',
        'unidad_medida',

        //precio al detal
        'precio_venta_detal',
        'precio_compra_detal',

        //precio al mayor
        'precio_venta_mayor',
        'precio_compra_mayor',
        'status',
        'registrado_por',
    ];

    /**
     * Get the inventario that owns the Producto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function categoria(): HasOne
    {
        return $this->hasOne(Categoria::class, 'id', 'categoria_id');
    }

    /**
     * Get the almacen associated with the Producto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function almacen(): HasOne
    {
        return $this->hasOne(User::class, 'producto_id', 'id');
    }

    /**
     * Get the inventario that owns the Producto
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventario(): BelongsTo
    {
        return $this->belongsTo(Inventario::class, 'id', 'producto_id');
    }

    /**
     * Get all of the movimiento_inventarios for the Producto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function movimiento_inventarios(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'producto_id', 'id');
    }

    /**
     * Get all of the comments for the Producto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function venta(): HasMany
    {
        return $this->hasMany(Venta::class, 'foreign_key', 'local_key');
    }

    public function detallesPedidos()
    {
        return $this->hasMany(PedidoDetalle::class);
    }

    //Modelo entidad relacion de las tablas productosm pedidos y detalles de pedidos en laravel 12 usando filament v3?

    
}