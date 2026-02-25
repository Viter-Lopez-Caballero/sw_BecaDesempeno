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
        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('position_type_id')->nullable()->after('status')->constrained('position_types')->onDelete('set null');
            $table->dropColumn('position_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('position_type')->nullable()->after('status');
            $table->dropConstrainedForeignId('position_type_id');
        });
    }
};
