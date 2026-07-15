<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('type')->after('target_id'); // tab category: escalations, status-updates, sla-alerts, user-creation
            $table->string('event')->after('type'); // display label: Escalation, Status Update, etc.
            $table->dropColumn('event_type'); // hindi na natin kailangan yung dati
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['type', 'event']);
            $table->string('event_type')->nullable();
        });
    }
};