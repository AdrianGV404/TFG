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
        // Cambiar enum a integer
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedSmallInteger('priority')->default(5)->change();
        });

        // Si quieres mapear los valores antiguos:
        $map = [
            'very_high' => 0,
            'high' => 2, // promedio de 1-2-3
            'mid' => 5,  // promedio 4-5-6
            'low' => 8,  // promedio 7-8
            'very_low' => 10
        ];
        \App\Models\Task::all()->each(function ($task) use ($map) {
            if (isset($map[$task->priority])) {
                $task->priority = $map[$task->priority];
                $task->save();
            }
        });
    }

    public function down(): void
    {
        // opcional: volver a enum
    }
};
