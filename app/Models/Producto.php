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
        'exitencia_real',
        'marca',
        'modelo',
        'fecha_vencimiento',
        'unidad_medida',

        //precio al detal
        'precio_detal',
        'precio_compra_detal',
        'precio_venta_detal',

        //precio al mayor
        'precio_mayor',
        'precio_compra_mayor',
        'precio_venta_mayor',
        'status',
        'registrado_por',
    ];

    /**
     * Get the inventario that owns the Producto
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'id', 'categoria_id');
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

    
}