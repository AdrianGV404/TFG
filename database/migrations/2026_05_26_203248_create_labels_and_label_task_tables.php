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
        // 1. LIMPIEZA PREVENTIVA: Si por algún intento previo existiese la tabla vieja de tags, la borramos.
        Schema::dropIfExists('task_tag');

        // 2. CREACIÓN: Tabla principal de etiquetas (labels) estructurada por Tenant
        Schema::create('labels', function (Blueprint $table) {
            $table->id(); // PK ID
            
            // Relación con tu tabla de inquilinos/tenants (Borrado en cascada)
            $table->foreignId('tenant_id')
                  ->constrained('tenants')
                  ->onDelete('cascade');
            
            $table->string('name'); // Nombre de la etiqueta
            $table->timestamps();

            // Evita etiquetas con nombres duplicados dentro de un mismo tenant
            $table->unique(['tenant_id', 'name']);
        });

        // 3. PIVOTE: Tabla intermedia para la relación Muchos a Muchos (label_task)
        Schema::create('label_task', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('label_id')
                  ->constrained('labels')
                  ->onDelete('cascade');

            $table->foreignId('task_id')
                  ->constrained('tasks')
                  ->onDelete('cascade');
                  
            $table->timestamps();

            // Evita asociar la misma etiqueta más de una vez a la misma tarea
            $table->unique(['label_id', 'task_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminamos en orden inverso debido a las restricciones de claves foráneas
        Schema::dropIfExists('label_task');
        Schema::dropIfExists('labels');
    }
};