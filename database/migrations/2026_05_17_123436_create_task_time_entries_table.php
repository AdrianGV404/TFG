<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_time_entries', function (Blueprint $table) {

            $table->id();

            $table->foreignId('task_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Inicio tracking
            $table->timestamp('started_at');

            // Fin tracking
            $table->timestamp('ended_at')->nullable();

            // Duración final en segundos
            $table->unsignedInteger('duration_seconds')->default(0);

            // Tracking activo
            $table->boolean('is_running')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_time_entries');
    }
};