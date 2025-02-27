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
        Schema::create('movimiento_inventarios', function (Blueprint $table) {
            $table->id();
            //codigo
            $table->string('codigo')->unique();
            $table->string('producto_id');
            $table->string('categoria_id');
            $table->integer('cantidad');
            $table->string('tipo'); //entrada, salidas, reposicion, venta
            $table->string('fecha_movimiento');
            $table->string('registrado_por');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_inventarios');
    }
};