<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('cliente_id')->unique();
            $table->string('vendedor_id')->unique();
            $table->string('fecha');
            $table->string('metodo_pago');
            $table->decimal('monto_usd',8,2)->default(0.00);
            $table->decimal('monto_bsd',8,2)->default(0.00);
            $table->decimal('totaL_venta',8,2)->default(0.00);
            $table->decimal('tasa_bcv',8,2)->default(0.00);
            $table->decimal('comision_usd',8,2)->default(0.00);
            $table->decimal('comision_bsd',8,2)->default(0.00);
            $table->string('registrado_por');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};