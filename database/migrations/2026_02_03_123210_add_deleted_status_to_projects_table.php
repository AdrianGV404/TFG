<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar 'deleted' al enum status
        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('active','archived','deleted') DEFAULT 'active'");
    }

    public function down(): void
    {
        // Volver a dejar solo active y archived
        DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('active','archived') DEFAULT 'active'");
    }
};
