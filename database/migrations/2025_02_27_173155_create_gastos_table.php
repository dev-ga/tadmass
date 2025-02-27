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
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->string('nro_gasto')->unique();
            $table->string('codigo')->unique();
            $table->string('descripcion');
            $table->string('tipo_gasto');
            $table->string('numero_factura')->unique();
            $table->string('nro_control')->unique();
            $table->string('fecha');
            $table->string('fecha_factura');
            $table->integer('proveedor_id')->nullable();
            $table->string('metodo_pago');
            $table->decimal('tasa_bcv', 8, 2)->default(0.00);
            $table->decimal('monto_usd', 8, 2)->default(0.00);
            $table->decimal('monto_bsd', 8, 2)->default(0.00);
            $table->decimal('iva', 8, 2)->default(0.00);
            $table->decimal('total_gasto_bsd', 8, 2)->default(0.00);
            $table->decimal('conversion_usd', 8, 2)->default(0.00);
            $table->string('registrado_por')->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};