<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatalogDocument;

class CatalogDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documents = [
            [
                'name' => 'Carta de Exclusividad Laboral',
                'description' => 'Documento firmado declarando exclusividad con TecNM, manifestando no exceder las 12 horas-semana en otras instituciones.',
                'active' => true,
                'is_fundamental' => true,
            ],
            [
                'name' => 'Liberación de Actividades Académicas',
                'description' => 'Constancia de cumplimiento de actividades académicas: reuniones, programas de formación, asesorías, propuestas de mejora y participación en eventos.',
                'active' => true,
                'is_fundamental' => true,
            ],
            [
                'name' => 'Constancia Actividades Frente a Grupo',
                'description' => 'Certificación de cumplimiento de actividades docentes: dosificación, evaluación de programas, entrega de informes y calificaciones.',
                'active' => true,
                'is_fundamental' => true,
            ],
            [
                'name' => 'Cédula Profesional',
                'description' => 'Copia digital de su Cédula Profesional vigente.',
                'active' => true,
                'is_fundamental' => true,
            ],
            [
                'name' => 'Comprobante de Domicilio',
                'description' => 'Comprobante de domicilio no mayor a 3 meses (luz, agua, teléfono).',
                'active' => true,
                'is_fundamental' => true,
            ],
            [
                'name' => 'Acta de Nacimiento',
                'description' => 'Documento oficial que registra el nacimiento de una persona.',
                'active' => true,
                'is_fundamental' => true,
            ],
            [
                'name' => 'CURP',
                'description' => 'Clave Única de Registro de Población.',
                'active' => true,
                'is_fundamental' => true,
            ],
            [
                'name' => 'Estado de Cuenta Bancaria',
                'description' => 'Estado de cuenta reciente para depósito de beca (no mayor a 3 meses).',
                'active' => true,
                'is_fundamental' => true,
            ],
        ];

        foreach ($documents as $document) {
            CatalogDocument::updateOrCreate(
                ['name' => $document['name']],
                $document
            );
        }

        $this->command->info('Catalog documents seeded successfully.');
    }
}
