<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    //
    protected $table = 'configuracions';

    protected $fillable = [
        'iva',
        'isrl',
        'porcen_venta_detal',
        'porcen_venta_mayor',
        'porcen_venta_general',
        'sueldo_base_vendedores',
        'graficos_filament_heading',
        'graficos_filament_getType',
        'graficos_dataset_label',
        'graficos_dataset_borderColor',
        'graficos_dataset_backgroundColor',
        'graficos_dataset_backgroundColor_array',
        'graficos_dataset_fill',
        'graficos_filament_geDescriptiont',
        'graficos_option_scale_x_display',
        'graficos_option_scale_y_display',
        'graficos_option_scale_x_ticks_stepSize',
        'graficos_option_scale_y_ticks_stepSize',
        'graficos_option_scale_indexAxis_x_y',
        'graficos_option_plugins_legend_display',
        'graficos_option_plugins_legend_position',
        'graficos_option_plugins_legend_align',
        'dash_panel_footer',
        'dash_panel_topBar_end',
        'dash_panel_topBar_start',
        'dash_panel_sideBar_nav_start',
        'dash_panel_page_header_action_before',
        'dash_panel_page_header_action_after',
        'dash_panel_titulo',
        'fecha_update_tasa',
    ];
}