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
        Schema::create('calendar_data', function (Blueprint $table) {
            $table->id();
            $table->integer('date');
            $table->integer('month');
            $table->integer('year');
            $table->string('hour');
            $table->string('minute');
            $table->string('ampm');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_data');
    }
};