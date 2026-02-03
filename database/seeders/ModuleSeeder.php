<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name' => 'Inicio',
                'description' => 'Módulo de inicio y dashboard principal',
                'key' => 'dashboard',
            ],
            [
                'name' => 'Control de solicitudes',
                'description' => 'Gestión y seguimiento de solicitudes de becas',
                'key' => 'controlsoli',
            ],
            [
                'name' => 'Convocatorias',
                'description' => 'Administración de convocatorias activas e historial',
                'key' => 'convo',
            ],
            [
                'name' => 'Modulo de Seguridad',
                'description' => 'Gestión de usuarios, roles, permisos y módulos del sistema',
                'key' => 'seg',
            ],
            [
                'name' => 'Catálogo',
                'description' => 'Módulo de catálogo',
                'key' => 'cat',
            ],
            [
                'name' => 'Docente',
                'description' => 'Módulo exclusivo para docentes',
                'key' => 'docente',
            ]
        ];

        foreach ($modules as $module) {
            Module::firstOrCreate(
                ['key' => $module['key']],
                [
                    'name' => $module['name'],
                    'description' => $module['description'],
                ]
            );
        }
    }
}