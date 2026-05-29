<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            // Relación uno a uno o uno a muchos con el usuario
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Permiso de Proyectos
            $table->boolean('can_create_projects')->default(false);
            
            // Permisos de Creación de Tareas
            $table->boolean('can_create_project_tasks_by_others')->default(false);
            $table->boolean('can_create_any_task')->default(false);
            
            // Permisos de Edición de Tareas
            $table->boolean('can_edit_project_tasks_by_others')->default(false);
            $table->boolean('can_edit_any_task')->default(false);
            
            // Permisos de Borrado de Tareas
            $table->boolean('can_delete_project_tasks_by_others')->default(false);
            $table->boolean('can_delete_any_task')->default(false);
            
            // Permiso de Reasignación
            $table->boolean('can_reassign_users')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};