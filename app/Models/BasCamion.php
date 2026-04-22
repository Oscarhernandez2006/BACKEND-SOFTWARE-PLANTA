<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasCamion extends Model
{
    protected $table = 'bas_camion';
    protected $primaryKey = 'bas_camion_id_registro';

    protected $fillable = [
        'bas_camion_fecha_movilizacion',
        'bas_camion_guia_movilizacion',
        'bas_camion_lugar_procedencia',
        'bas_camion_ganado_proveedor',
        'bas_camion_ganado_cliente',
        'bas_camion_placa_vehiculo',
        'bas_camion_conductor_nombre',
        'bas_camion_referencia',
        'bas_camion_cantidad_lote',
        'bas_camion_peso_entrada',
        'bas_camion_peso_salida',
        'bas_camion_peso_neto',
        'bas_camion_peso_promedio',
    ];

    protected function casts(): array
    {
        return [
            'bas_camion_fecha_movilizacion' => 'datetime',
            'bas_camion_referencia' => 'integer',
            'bas_camion_cantidad_lote' => 'integer',
            'bas_camion_peso_entrada' => 'decimal:2',
            'bas_camion_peso_salida' => 'decimal:2',
            'bas_camion_peso_neto' => 'decimal:2',
            'bas_camion_peso_promedio' => 'decimal:2',
        ];
    }
}
