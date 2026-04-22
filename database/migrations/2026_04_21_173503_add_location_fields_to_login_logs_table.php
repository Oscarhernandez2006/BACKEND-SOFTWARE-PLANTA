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
        Schema::table('login_logs', function (Blueprint $table) {
            $table->string('country')->nullable()->after('device_type');
            $table->string('region')->nullable()->after('country');
            $table->string('city')->nullable()->after('region');
            $table->string('isp')->nullable()->after('city');
            $table->decimal('latitude', 10, 7)->nullable()->after('isp');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_logs', function (Blueprint $table) {
            $table->dropColumn(['country', 'region', 'city', 'isp', 'latitude', 'longitude']);
        });
    }
};
