<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('date')->nullable();
            $table->enum('type', ['mail', 'phone', 'chat'])->default('mail');
            $table->string('subject')->nullable();
            $table->string('staff')->nullable();
            $table->enum('status', ['completed', 'pending', 'resolved', 'cancelled'])->default('pending');
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->string('resp_time')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('communications');
    }
};