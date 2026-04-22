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
        Schema::create('bas_camion', function (Blueprint $table) {
            $table->id('bas_camion_id_registro');
            $table->timestamp('bas_camion_fecha_movilizacion');
            $table->string('bas_camion_guia_movilizacion', 50);
            $table->string('bas_camion_lugar_procedencia', 150);
            $table->string('bas_camion_ganado_proveedor', 150);
            $table->string('bas_camion_ganado_cliente', 150);
            $table->string('bas_camion_placa_vehiculo', 10);
            $table->string('bas_camion_conductor_nombre', 100);
            $table->integer('bas_camion_referencia');
            $table->integer('bas_camion_cantidad_lote');
            $table->decimal('bas_camion_peso_entrada');
            $table->decimal('bas_camion_peso_salida');
            $table->decimal('bas_camion_peso_neto');
            $table->decimal('bas_camion_peso_promedio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bas_camion');
    }
};
