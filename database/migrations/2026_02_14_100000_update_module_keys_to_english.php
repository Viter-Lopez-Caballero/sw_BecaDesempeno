<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update Module Keys to English
        DB::table('modules')->where('key', 'adminsoli')->update(['key' => 'applications']);
        DB::table('modules')->where('key', 'controlsoli')->update(['key' => 'request_control']);
        DB::table('modules')->where('key', 'convo')->update(['key' => 'announcements']);
        DB::table('modules')->where('key', 'seg')->update(['key' => 'security']);
        DB::table('modules')->where('key', 'cat')->update(['key' => 'catalog']);
        DB::table('modules')->where('key', 'reconocimiento')->update(['key' => 'recognitions']);
        // evaluaciones matches, but just in case of typo or case
        DB::table('modules')->where('key', 'evaluaciones')->update(['key' => 'evaluations']);

        // Update Permission keys (specifically requests to request_control)
        // Requests were previously seeded under 'applications' or just mismatched.
        // We move 'requests.*' permissions to 'request_control' module_key
        DB::table('permissions')
            ->where('name', 'like', 'requests.%')
            ->update(['module_key' => 'request_control']);
            
        // Ensure other permissions allow matching just in case
        // Security
        DB::table('permissions')->where('module_key', 'seg')->update(['module_key' => 'security']);
        // Catalog
        DB::table('permissions')->where('module_key', 'cat')->update(['module_key' => 'catalog']); 
        // Announcements
        DB::table('permissions')->where('module_key', 'convo')->update(['module_key' => 'announcements']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert keys if needed
        DB::table('modules')->where('key', 'applications')->update(['key' => 'adminsoli']);
        DB::table('modules')->where('key', 'request_control')->update(['key' => 'controlsoli']);
        DB::table('modules')->where('key', 'announcements')->update(['key' => 'convo']);
        DB::table('modules')->where('key', 'security')->update(['key' => 'seg']);
        DB::table('modules')->where('key', 'catalog')->update(['key' => 'cat']);
        DB::table('modules')->where('key', 'recognitions')->update(['key' => 'reconocimiento']);
        DB::table('modules')->where('key', 'evaluations')->update(['key' => 'evaluaciones']);

        DB::table('permissions')
            ->where('name', 'like', 'requests.%')
            ->update(['module_key' => 'applications']);
    }
};
