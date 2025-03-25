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
        Schema::create('gasto_detalles', function (Blueprint $table) {
            $table->id();
            $table->integer('gasto_id');
            $table->decimal('monto_usd', 8, 2)->default(0.00);
            $table->decimal('monto_bsd', 8, 2)->default(0.00);
            $table->string('concepto');
            $table->string('registrado_por');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gasto_detalles');
    }
};