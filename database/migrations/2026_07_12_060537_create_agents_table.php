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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role'); // "Senior Agent" or "Support Agent"
            $table->string('team')->nullable(); // for "All Teams" filter dropdown
            $table->string('avatar')->nullable(); // image path, null pwede pag initials na lang gamit (e.g. "M" avatar)
            $table->enum('active_status', ['online', 'offline', 'away'])->default('online');
            $table->integer('total_assigned')->default(0);
            $table->integer('total_resolved')->default(0);
            $table->integer('avg_response_minutes')->default(0); // store as number, i-format sa Blade na "15m"
            $table->decimal('csat_score', 2, 1)->default(0); // e.g. 4.9
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
