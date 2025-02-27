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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            //codigo
            $table->string('codigo')->unique();
            //quiero crear los campos de una tabla para almacener productos medico quirurjicos?
            $table->string('nombre');
            $table->string('descripcion');
            $table->string('categoria_id');
            $table->string('imagen')->nullable();
            $table->string('exitencia_real');
            $table->string('marca');
            $table->string('modelo');
            $table->string('fecha_vencimiento');
            $table->string('unidad_medida');

            //precio al detal
            $table->decimal('precio_detal', 10, 2)->default(0.00);
            $table->decimal('precio_compra_detal', 10, 2)->default(0.00);
            $table->decimal('precio_venta_detal', 10, 2)->default(0.00);

            //precio al mayor
            $table->decimal('precio_mayor', 10, 2)->default(0.00);
            $table->decimal('precio_compra_mayor', 10, 2)->default(0.00);
            $table->decimal('precio_venta_mayor', 10, 2)->default(0.00);
            
            $table->string('status');
            $table->string('registrado_por');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};