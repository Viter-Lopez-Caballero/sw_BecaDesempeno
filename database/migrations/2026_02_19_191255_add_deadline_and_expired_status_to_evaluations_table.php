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
        Schema::table('evaluations', function (Blueprint $table) {
            $table->timestamp('deadline_at')->nullable()->after('status');
        });

        // Add 'expired' to status enum
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE evaluations MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'expired') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status enum
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE evaluations MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");

        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn('deadline_at');
        });
    }
};
