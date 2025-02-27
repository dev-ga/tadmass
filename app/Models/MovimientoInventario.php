<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    //table
    protected $table = 'movimiento_inventario';

    //fillable
    protected $fillable = [
        'inventario_id',
        'producto_id',
        'categoria_id',
        'cantidad',
        'tipo',
        'fecha_movimiento',
        'registrado_por',
    ];

    /**
     * Get the prodcuto that owns the MovimientoInventario
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }

    /**
     * Get the inventario that owns the MovimientoInventario
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventario(): BelongsTo
    {
        return $this->belongsTo(Inventario::class, 'inventario_id', 'id');
    }
}