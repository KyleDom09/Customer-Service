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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('target_id'); // e.g. "Ticket #1004"
            $table->enum('event_type', ['escalation', 'status_update', 'creation', 'sla_alert', 'user_creation']);
            $table->text('description');
            $table->string('performed_by'); // e.g. "System Workflow", "Dominick", "Customer Guest"
            $table->enum('severity', ['critical', 'high', 'medium', 'low']);
            $table->timestamp('logged_at'); // basehan ng "Just now", "10 mins ago", etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
