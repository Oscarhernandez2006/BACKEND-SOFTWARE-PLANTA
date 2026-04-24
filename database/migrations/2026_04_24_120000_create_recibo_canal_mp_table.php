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
        Schema::create('recibo_canal_mp', function (Blueprint $table) {
            $table->id('recibo_canal_mp_id_registro');
            $table->unsignedBigInteger('orden_ingreso_mp_id_registro');
            $table->date('recibo_ingreso_mp_fecha_registro');
            $table->string('recibo_ingreso_mp_nombre_cliente', 100);
            $table->string('recibo_canal_mp_numero_guia', 100);
            $table->integer('recibo_canal_mp_lote_orden_sacrificio');
            $table->integer('recibo_canal_mp_numero_identificacion');
            $table->string('recibo_canal_mp_cavas_canales', 50);
            $table->string('recibo_canal_mp_producto', 100)->default('CARNE EN CANAL');
            $table->integer('recibo_canal_mp_numero_orden');
            $table->decimal('recibo_canal_mp_peso_canal', 10, 3);
            $table->timestamps();

            $table->foreign('orden_ingreso_mp_id_registro')
                  ->references('orden_ingreso_mp_id_registro')
                  ->on('orden_ingreso_mp')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recibo_canal_mp');
    }
};
