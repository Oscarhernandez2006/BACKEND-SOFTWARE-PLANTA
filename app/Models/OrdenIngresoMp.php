<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenIngresoMp extends Model
{
    protected $table = 'orden_ingreso_mp';
    protected $primaryKey = 'orden_ingreso_mp_id_registro';

    protected $fillable = [
        'orden_ingreso_mp_numero_orden',
        'orden_ingreso_mp_tipo_proceso',
        'orden_ingreso_mp_fecha_sacrificio',
        'orden_ingreso_mp_fecha_proceso',
        'orden_ingreso_mp_nombre_cliente',
        'orden_ingreso_mp_estado_orden',
    ];

    protected function casts(): array
    {
        return [
            'orden_ingreso_mp_numero_orden' => 'integer',
            'orden_ingreso_mp_fecha_sacrificio' => 'date',
            'orden_ingreso_mp_fecha_proceso' => 'date',
        ];
    }
}
