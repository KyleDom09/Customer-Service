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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();

            // Customer info
            $table->string('customer_name');
            $table->string('customer_email');

            // Ticket content
            $table->string('subject');
            $table->string('sub_subject')->nullable();
            $table->string('category');
            $table->text('description')->nullable();

            // Assignment (relational, FK to agents table)
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();

            // Classification
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])->default('LOW');
            $table->enum('status', ['OPEN', 'PENDING', 'IN PROGRESS', 'RESOLVED', 'CLOSED'])->default('OPEN');

            // Response/resolution tracking
            $table->integer('response_minutes')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};