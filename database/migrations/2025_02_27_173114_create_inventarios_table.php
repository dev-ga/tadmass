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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->string('almacen_id');
            $table->string('producto_id');
            $table->string('categoria_id');
            $table->integer('existencia');
            $table->decimal('precio_venta_mayor', 10, 2)->default(0.00);
            $table->decimal('precio_venta_detal', 10, 2)->default(0.00);
            $table->string('registrado_por');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};