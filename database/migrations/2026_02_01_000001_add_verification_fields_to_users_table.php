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
        Schema::table('users', function (Blueprint $table) {
            $table->string('curp', 18)->unique()->nullable()->after('email');
            $table->string('apellido_paterno')->nullable()->after('name');
            $table->string('apellido_materno')->nullable()->after('apellido_paterno');
            $table->string('email_verification_code', 6)->nullable()->after('email_verified_at');
            $table->timestamp('email_verification_code_expires_at')->nullable()->after('email_verification_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'curp',
                'apellido_paterno',
                'apellido_materno',
                'email_verification_code',
                'email_verification_code_expires_at'
            ]);
        });
    }
};
