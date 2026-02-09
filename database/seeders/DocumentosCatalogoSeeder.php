<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentoCatalogo;

class DocumentosCatalogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentos = [
            [
                'nombre' => 'Carta de Exclusividad Laboral',
                'descripcion' => 'Documento firmado declarando exclusividad con TecNM, manifestando no exceder las 12 horas-semana en otras instituciones.',
                'activo' => true,
                'es_fundamental' => true,
            ],
            [
                'nombre' => 'Liberación de Actividades Académicas',
                'descripcion' => 'Constancia de cumplimiento de actividades académicas: reuniones, programas de formación, asesorías, propuestas de mejora y participación en eventos.',
                'activo' => true,
                'es_fundamental' => true,
            ],
            [
                'nombre' => 'Constancia Actividades Frente a Grupo',
                'descripcion' => 'Certificación de cumplimiento de actividades docentes: dosificación, evaluación de programas, entrega de informes y calificaciones.',
                'activo' => true,
                'es_fundamental' => true,
            ],
            [
                'nombre' => 'Cédula Profesional',
                'descripcion' => 'Copia digital de su Cédula Profesional vigente.',
                'activo' => true,
                'es_fundamental' => true,
            ],
            [
                'nombre' => 'Comprobante de Domicilio',
                'descripcion' => 'Comprobante de domicilio no mayor a 3 meses (luz, agua, teléfono).',
                'activo' => true,
                'es_fundamental' => true,
            ],
            [
                'nombre' => 'Acta de Nacimiento',
                'descripcion' => 'Documento oficial que registra el nacimiento de una persona.',
                'activo' => true,
                'es_fundamental' => true,
            ],
            [
                'nombre' => 'CURP',
                'descripcion' => 'Clave Única de Registro de Población.',
                'activo' => true,
                'es_fundamental' => true,
            ],
            [
                'nombre' => 'Estado de Cuenta Bancaria',
                'descripcion' => 'Estado de cuenta reciente para depósito de beca (no mayor a 3 meses).',
                'activo' => true,
                'es_fundamental' => true,
            ],
        ];

        foreach ($documentos as $documento) {
            DocumentoCatalogo::updateOrCreate(
                ['nombre' => $documento['nombre']], // Buscar por nombre
                $documento // Actualizar o crear con estos datos
            );
        }

        $this->command->info('Documentos de catálogo creados/actualizados exitosamente');
    }
}
