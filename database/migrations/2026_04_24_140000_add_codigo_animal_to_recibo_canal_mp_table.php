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
        Schema::table('recibo_canal_mp', function (Blueprint $table) {
            $table->string('recibo_canal_mp_codigo_animal', 50)->after('recibo_canal_mp_numero_identificacion');

            // Único por orden: no puede haber dos canales con el mismo código en la misma orden
            $table->unique(
                ['orden_ingreso_mp_id_registro', 'recibo_canal_mp_codigo_animal'],
                'recibo_canal_orden_codigo_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recibo_canal_mp', function (Blueprint $table) {
            $table->dropUnique('recibo_canal_orden_codigo_unique');
            $table->dropColumn('recibo_canal_mp_codigo_animal');
        });
    }
};
