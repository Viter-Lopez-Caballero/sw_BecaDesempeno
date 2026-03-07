<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Limpiar archivos físicos generados por datos de prueba
        foreach (['announcements', 'templates', 'documents', 'catalog_documents'] as $folder) {
            if (Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->deleteDirectory($folder);
            }
        }

        // User::factory(10)->create();

        /* User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); */

        $this->call([
            ModuleSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            StateSeeder::class,
            InstitutionSeeder::class,
            PriorityAreaSeeder::class,
            SubAreaSeeder::class,
            RenapoSeeder::class,
            CatalogDocumentSeeder::class,
            //AnnouncementSeeder::class,
            PositionTypeSeeder::class,
            UserSeeder::class,
        ]);
    }
}
