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
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('cedula')->index();
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->string('platform')->nullable();       // Windows, macOS, Linux, Android, iOS
            $table->string('browser')->nullable();         // Chrome, Firefox, Edge, etc.
            $table->string('device_type')->nullable();     // desktop, mobile, tablet
            $table->enum('status', ['success', 'failed'])->default('failed');
            $table->string('failure_reason')->nullable();  // credenciales incorrectas, cuenta bloqueada, etc.
            $table->timestamp('logged_in_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
