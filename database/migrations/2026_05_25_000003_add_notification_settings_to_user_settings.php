<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            // Canal de entrega: 'app', 'email', 'both'
            $table->string('notif_channel')->default('app')->after('notif_reports');

            // Notificación cuando otro usuario cambia el estado de una tarea
            $table->boolean('notif_status_change')->default(true)->after('notif_channel');
        });
    }

    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn(['notif_channel', 'notif_status_change']);
        });
    }
};
