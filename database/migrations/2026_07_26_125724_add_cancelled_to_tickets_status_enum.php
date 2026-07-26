<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY status ENUM('OPEN','PENDING','IN PROGRESS','RESOLVED','CLOSED','CANCELLED') NOT NULL DEFAULT 'OPEN'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY status ENUM('OPEN','PENDING','IN PROGRESS','RESOLVED','CLOSED') NOT NULL DEFAULT 'OPEN'");
    }
};