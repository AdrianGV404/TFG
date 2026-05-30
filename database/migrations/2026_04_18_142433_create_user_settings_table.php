<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            // Relación 1:1 con User
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            
            // Columnas individuales (mejor que JSON para consultas rápidas)
            $table->string('theme')->default('light');
            $table->boolean('notif_tasks')->default(false);
            $table->boolean('notif_alerts')->default(false);
            $table->boolean('notif_reports')->default(false);
            $table->integer('retention_days')->default(90);
            $table->boolean('allow_employee_tags')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_settings');
    }
};