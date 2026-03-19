<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'type' => 'recognition',
                'name' => 'Plantilla Reconocimiento',
                'description' => 'Plantilla base para generar reconocimientos.',
                'source_file' => database_path('seeders/templates/reconocimiento.pdf'),
                'target_file' => 'templates/template_reconocimiento.pdf',
                'content_data' => [
                    'body_text' => "Por su destacada participacion como miembro de la Comision de Evaluacion\nLocal y Nacional al Programa de Estimulo al Desempeno del Personal\nDocente para los Institutos Federales y Centros",
                    'director_name' => 'RAMON JIMENEZ LOPEZ',
                    'director_title' => 'DIRECTOR GENERAL',
                ],
            ],
            [
                'type' => 'acceptance',
                'name' => 'Plantilla Carta de Aceptacion',
                'description' => 'Plantilla base para generar cartas de aceptacion.',
                'source_file' => database_path('seeders/templates/carta_aceptacion.pdf'),
                'target_file' => 'templates/template_carta_aceptacion.pdf',
                'content_data' => [
                    'body_text' => 'De conformidad a su solicitud de la convocatoria vigente, se notifica el resultado de su evaluacion y nivel asignado conforme a lineamientos.',
                    'director_name' => 'RAMON JIMENEZ LOPEZ',
                    'director_title' => 'DIRECTOR GENERAL DEL TecNM',
                ],
            ],
        ];

        foreach ($templates as $templateConfig) {
            if (!file_exists($templateConfig['source_file'])) {
                if ($this->command) {
                    $this->command->warn("No se encontro el archivo de plantilla: {$templateConfig['source_file']}");
                }
                continue;
            }

            Storage::disk('public')->put(
                $templateConfig['target_file'],
                file_get_contents($templateConfig['source_file'])
            );

            Template::where('type', $templateConfig['type'])
                ->where('name', '!=', $templateConfig['name'])
                ->update(['is_active' => false]);

            Template::updateOrCreate(
                [
                    'type' => $templateConfig['type'],
                    'name' => $templateConfig['name'],
                ],
                [
                    'description' => $templateConfig['description'],
                    'file_path' => $templateConfig['target_file'],
                    'file_name' => basename($templateConfig['source_file']),
                    'is_active' => true,
                    'content_data' => $templateConfig['content_data'],
                ]
            );

            if ($this->command) {
                $this->command->info("Plantilla sembrada: {$templateConfig['name']}");
            }
        }
    }
}
