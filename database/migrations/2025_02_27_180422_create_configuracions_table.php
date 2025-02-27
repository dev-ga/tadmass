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
        Schema::create('configuracions', function (Blueprint $table) {
            $table->id();
            $table->decimal('iva', 8, 2)->default(0.00);
            $table->decimal('isrl', 8, 2)->default(0.00);
            $table->decimal('porcen_venta_detal', 8, 2)->default(0.00);
            $table->decimal('porcen_venta_mayor', 8, 2)->default(0.00);
            $table->decimal('porcen_venta_general', 8, 2)->default(0.00);
            $table->decimal('sueldo_base_vendedores', 8, 2)->default(0.00);
            $table->string('graficos_filament_heading')->default('Grafico');
            $table->string('graficos_filament_getType')->default('bar');
            $table->string('graficos_dataset_label')->nullable();
            $table->string('graficos_dataset_borderColor')->nullable();
            $table->string('graficos_dataset_backgroundColor')->nullable();
            $table->string('graficos_dataset_backgroundColor_array')->nullable();
            $table->string('graficos_dataset_fill')->nullable();
            $table->string('graficos_filament_geDescriptiont')->nullable();
            $table->string('graficos_option_scale_x_display')->nullable();
            $table->string('graficos_option_scale_y_display')->nullable();
            $table->integer('graficos_option_scale_x_ticks_stepSize')->nullable();
            $table->integer('graficos_option_scale_y_ticks_stepSize')->nullable();
            $table->integer('graficos_option_scale_indexAxis_x_y')->nullable();
            $table->string('graficos_option_plugins_legend_display')->nullable();
            $table->string('graficos_option_plugins_legend_position')->nullable();
            $table->string('graficos_option_plugins_legend_align')->nullable();
            $table->string('dash_panel_footer')->nullable('footer');
            $table->string('dash_panel_topBar_end')->nullable();
            $table->string('dash_panel_topBar_start')->nullable();
            $table->string('dash_panel_sideBar_nav_start')->nullable();
            $table->string('dash_panel_page_header_action_before')->nullable();
            $table->string('dash_panel_page_header_action_after')->nullable();
            $table->string('dash_panel_titulo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracions');
    }
};