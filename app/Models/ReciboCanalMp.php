<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReciboCanalMp extends Model
{
    protected $table = 'recibo_canal_mp';
    protected $primaryKey = 'recibo_canal_mp_id_registro';

    protected $fillable = [
        'orden_ingreso_mp_id_registro',
        'recibo_ingreso_mp_fecha_registro',
        'recibo_ingreso_mp_nombre_cliente',
        'recibo_canal_mp_numero_guia',
        'recibo_canal_mp_lote_orden_sacrificio',
        'recibo_canal_mp_numero_identificacion',
        'recibo_canal_mp_cavas_canales',
        'recibo_canal_mp_producto',
        'recibo_canal_mp_numero_orden',
        'recibo_canal_mp_peso_canal',
    ];

    protected function casts(): array
    {
        return [
            'recibo_ingreso_mp_fecha_registro' => 'date',
            'recibo_canal_mp_lote_orden_sacrificio' => 'integer',
            'recibo_canal_mp_numero_identificacion' => 'integer',
            'recibo_canal_mp_numero_orden' => 'integer',
            'recibo_canal_mp_peso_canal' => 'decimal:3',
        ];
    }

    public function orden(): BelongsTo
    {
        return $this->belongsTo(OrdenIngresoMp::class, 'orden_ingreso_mp_id_registro', 'orden_ingreso_mp_id_registro');
    }
}
