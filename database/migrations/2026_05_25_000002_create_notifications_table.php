<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Tipo de evento que generó la notificación
            $table->enum('type', ['assignment', 'due_soon', 'status_change']);

            $table->string('title');
            $table->string('body');

            // Datos extra: task_id, project_id, etc.
            $table->json('data')->nullable();

            // null = no leída; timestamp = leída
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
