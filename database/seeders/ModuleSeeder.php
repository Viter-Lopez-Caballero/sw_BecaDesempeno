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
                'name' => 'Solicitudes',
                'description' => 'Gestión individual de solicitudes de becas y estímulos',
                'key' => 'adminsoli',
            ],
            [
                'name' => 'Control de solicitudes',
                'description' => 'Resumen y control de solicitudes por institución',
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
                'name' => 'Reconocimientos',
                'description' => 'Gestión de reconocimientos para evaluadores',
                'key' => 'reconocimiento',
            ],

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