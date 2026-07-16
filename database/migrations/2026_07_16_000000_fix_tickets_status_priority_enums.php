<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ito ay nag-aayos ng mismatch sa pagitan ng aktwal na enum sa database
     * (lowercase: open, pending, resolved, overdue) at ng ginagamit ng
     * application code (uppercase: OPEN, PENDING, IN PROGRESS, RESOLVED, CLOSED).
     *
     * Ang "overdue" ay hindi na ginagamit bilang status - ang mga existing
     * tickets na naka-"overdue" ay ilalagay muna sa "OPEN" bago tanggalin
     * ang value na ito sa enum.
     */
    public function up(): void
    {
        // 1. I-convert muna ang mga existing "overdue" tickets papuntang "open"
        DB::statement("UPDATE tickets SET status = 'open' WHERE status = 'overdue'");

        // 2. I-convert ang natitirang lowercase data papuntang UPPERCASE
        //    bago natin baguhin ang enum definition (para hindi ma-truncate)
        DB::statement("UPDATE tickets SET status = UPPER(status)");
        DB::statement("UPDATE tickets SET priority = UPPER(priority)");

        // 3. I-expand ang enum column para tanggapin ang lahat ng UPPERCASE
        //    values na kailangan ng application (walang OVERDUE)
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('OPEN', 'PENDING', 'IN PROGRESS', 'RESOLVED', 'CLOSED') NOT NULL DEFAULT 'OPEN'");
        DB::statement("ALTER TABLE tickets MODIFY COLUMN priority ENUM('LOW', 'MEDIUM', 'HIGH', 'CRITICAL') NOT NULL DEFAULT 'LOW'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('open', 'pending', 'resolved', 'overdue') NOT NULL DEFAULT 'open'");
        DB::statement("ALTER TABLE tickets MODIFY COLUMN priority ENUM('critical', 'high', 'medium', 'low') NOT NULL DEFAULT 'medium'");

        DB::statement("UPDATE tickets SET status = LOWER(status)");
        DB::statement("UPDATE tickets SET priority = LOWER(priority)");
    }
};
