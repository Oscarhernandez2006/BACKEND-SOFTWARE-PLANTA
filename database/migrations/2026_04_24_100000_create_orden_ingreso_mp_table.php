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
        Schema::create('orden_ingreso_mp', function (Blueprint $table) {
            $table->id('orden_ingreso_mp_id_registro');
            $table->integer('orden_ingreso_mp_numero_orden')->unique();
            $table->string('orden_ingreso_mp_tipo_proceso', 50);
            $table->date('orden_ingreso_mp_fecha_sacrificio');
            $table->date('orden_ingreso_mp_fecha_proceso');
            $table->string('orden_ingreso_mp_nombre_cliente', 100);
            $table->string('orden_ingreso_mp_estado_orden', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_ingreso_mp');
    }
};
