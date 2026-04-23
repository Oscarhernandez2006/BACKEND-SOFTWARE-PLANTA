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
        Schema::create('bas_pie', function (Blueprint $table) {
            $table->id('bas_pie_id_registro');
            $table->unsignedBigInteger('bas_camion_id_registro');
            $table->date('bas_pie_fecha_movilizacion');
            $table->string('bas_pie_guia_movilizacion', 50);
            $table->string('bas_pie_ganado_proveedor', 150);
            $table->string('bas_pie_ganado_cliente', 150);
            $table->string('bas_pie_tipo_animal', 50);
            $table->string('bas_pie_ubicacion_corral', 50);
            $table->integer('bas_pie_lote_animales');
            $table->integer('bas_pie_numero_consecutivo_animal');
            $table->decimal('bas_pie_peso_animal', 10, 3);
            $table->string('bas_pie_observaciones', 500)->nullable();
            $table->timestamp('bas_pie_fecha_creacion')->useCurrent();
            $table->timestamps();

            $table->foreign('bas_camion_id_registro')
                  ->references('bas_camion_id_registro')
                  ->on('bas_camion')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bas_pie');
    }
};
