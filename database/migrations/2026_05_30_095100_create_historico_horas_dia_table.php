<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historico_horas_dia', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->date('dia');

            // Horas acumuladas ese día para ese proyecto/tenant
            // Usamos decimal para soportar fracciones de hora (ej: 1.5h)
            $table->decimal('horas', 8, 2)->default(0);

            $table->timestamps();

            // Clave única: un solo registro por combinación tenant+proyecto+día
            $table->unique(['tenant_id', 'project_id', 'dia']);

            // Índices para consultas frecuentes
            $table->index(['tenant_id', 'dia']);
            $table->index(['project_id', 'dia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_horas_dia');
    }
};