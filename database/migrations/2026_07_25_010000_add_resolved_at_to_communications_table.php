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
        Schema::table('communications', function (Blueprint $table) {
            if (!Schema::hasColumn('communications', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('resp_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('communications', function (Blueprint $table) {
            if (Schema::hasColumn('communications', 'resolved_at')) {
                $table->dropColumn('resolved_at');
            }
        });
    }
};