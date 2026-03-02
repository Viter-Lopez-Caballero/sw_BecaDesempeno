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
        Schema::table('recognitions', function (Blueprint $table) {
            $table->string('identifier')->nullable()->unique()->after('sent_at');
            $table->text('digital_seal')->nullable()->after('identifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recognitions', function (Blueprint $table) {
            $table->dropColumn(['identifier', 'digital_seal']);
        });
    }
};
