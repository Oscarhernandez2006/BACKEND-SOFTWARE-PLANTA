<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BasPie extends Model
{
    protected $table = 'bas_pie';
    protected $primaryKey = 'bas_pie_id_registro';

    protected $fillable = [
        'bas_camion_id_registro',
        'bas_pie_fecha_movilizacion',
        'bas_pie_guia_movilizacion',
        'bas_pie_ganado_proveedor',
        'bas_pie_ganado_cliente',
        'bas_pie_tipo_animal',
        'bas_pie_ubicacion_corral',
        'bas_pie_lote_animales',
        'bas_pie_numero_consecutivo_animal',
        'bas_pie_peso_animal',
        'bas_pie_observaciones',
    ];

    protected function casts(): array
    {
        return [
            'bas_pie_fecha_movilizacion' => 'date',
            'bas_pie_lote_animales' => 'integer',
            'bas_pie_numero_consecutivo_animal' => 'integer',
            'bas_pie_peso_animal' => 'decimal:3',
            'bas_pie_fecha_creacion' => 'datetime',
        ];
    }

    public function camion(): BelongsTo
    {
        return $this->belongsTo(BasCamion::class, 'bas_camion_id_registro', 'bas_camion_id_registro');
    }
}
