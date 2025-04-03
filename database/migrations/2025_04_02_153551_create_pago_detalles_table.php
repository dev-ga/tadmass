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
        Schema::create('pago_detalles', function (Blueprint $table) {
            $table->id();
            $table->integer('venta_id');
            $table->string('codigo_venta');
            $table->json('productos');
            $table->decimal('total_venta_bsd', 8, 2)->default(0.00);
            $table->decimal('total_venta_usd', 8, 2)->default(0.00);
            
            $table->decimal('efectivo_usd', 8, 2)->default(0.00);
            $table->decimal('zelle_usd', 8, 2)->default(0.00);
            $table->string('referencia_zelle_usd')->nullable();
            
            $table->decimal('pagoMovil_bsd', 8, 2)->default(0.00);
            $table->decimal('transferencia_bsd', 8, 2)->default(0.00);
            $table->decimal('efectivo_bsd', 8, 2)->default(0.00);
            $table->decimal('puntoVenta_bsd', 8, 2)->default(0.00);
            $table->string('referencia_puntoVenta_bsd')->nullable();
            $table->string('referencia_pagoMovil_bsd')->nullable();
            $table->string('referencia_transferencia_bsd')->nullable();
            $table->string('registrado_por')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_detalles');
    }
};