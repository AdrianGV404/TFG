<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Modificar el ENUM añadiendo 'on_hold' y 'testing'
        DB::statement("
            ALTER TABLE tasks
            MODIFY COLUMN status
            ENUM('pending','in_progress','on_hold','testing','done')
            NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // Revertir al enum original (cuidado: datos con on_hold/testing se perderán)
        DB::statement("
            ALTER TABLE tasks
            MODIFY COLUMN status
            ENUM('pending','in_progress','done')
            NOT NULL DEFAULT 'pending'
        ");
    }
};
