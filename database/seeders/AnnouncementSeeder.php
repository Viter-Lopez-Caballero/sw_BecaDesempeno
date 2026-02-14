<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Announcement;
use App\Models\Calendar;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Convocatoria Activa
        $active = Announcement::create([
            'name' => 'Beca de Excelencia 2024',
            'description' => 'Convocatoria para estudiantes con promedio mayor a 9.5.',
            'status' => 'activa',
        ]);

        Calendar::create([
            'announcement_id' => $active->id,
            'publication_start' => now()->subDays(5),
            'registration_start' => now()->subDays(2),
            'registration_end' => now()->addDays(10),
            'evaluation_start' => now()->addDays(15),
            'evaluation_end' => now()->addDays(20),
            'results_start' => now()->addDays(25),
            'results_end' => now()->addDays(30),
        ]);

        // 2. Convocatoria Pendiente (Futura)
        $pending = Announcement::create([
            'name' => 'Beca Deportiva 2024',
            'description' => 'Apoyo para deportistas de alto rendimiento.',
            'status' => 'pendiente',
        ]);

        Calendar::create([
            'announcement_id' => $pending->id,
            'publication_start' => now()->addDays(10),
            'registration_start' => now()->addDays(15),
            'registration_end' => now()->addDays(25),
            'evaluation_start' => now()->addDays(30),
            'evaluation_end' => now()->addDays(35),
            'results_start' => now()->addDays(40),
            'results_end' => now()->addDays(45),
        ]);

        // 3. Convocatoria Cerrada (Pasada)
        $closed = Announcement::create([
            'name' => 'Beca Transporte 2023',
            'description' => 'Apoyo económico para transporte público.',
            'status' => 'cerrada',
        ]);

        Calendar::create([
            'announcement_id' => $closed->id,
            'publication_start' => now()->subMonths(3),
            'registration_start' => now()->subMonths(2)->subDays(10),
            'registration_end' => now()->subMonths(2),
            'evaluation_start' => now()->subMonths(1)->subDays(20),
            'evaluation_end' => now()->subMonths(1)->subDays(10),
            'results_start' => now()->subMonths(1),
            'results_end' => now()->subMonths(1)->addDays(5),
        ]);
    }
}
