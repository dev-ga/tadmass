<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GastoDetalle extends Model
{
    protected $table = 'gasto_detalles';

    protected $fillable = [
        'gasto_id',
        'concepto',
        'monto_usd',
        'monto_bsd',
        'registrado_por',
    ];

    public function gasto(): BelongsTo
    {
        return $this->belongsTo(Gasto::class, 'gasto_id', 'id');
    }
}