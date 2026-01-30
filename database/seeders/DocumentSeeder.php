<?php

namespace Database\Seeders;

use App\Models\Convocatoria;
use App\Models\Documento;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Active Convocatoria
        $convocatoria = Convocatoria::create([
            'name' => 'Convocatoria Estímulos 2026',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_active' => true,
        ]);

        // 2. Get some existing users or create new ones
        $users = User::role('Docente')->take(5)->get();

        if ($users->isEmpty()) {
            return; // Or create dummy users if preferred, but existing setup implies users exist
        }

        // 3. Create Solicitudes and Documents for each user
        foreach ($users as $user) {
            // Check if solicitud already exists
            if (Solicitud::where('user_id', $user->id)->where('convocatoria_id', $convocatoria->id)->exists()) {
                continue;
            }

            $solicitud = Solicitud::create([
                'user_id' => $user->id,
                'convocatoria_id' => $convocatoria->id,
                'status' => 'pending',
            ]);

            // Create some dummy documents
            $documents = [
                ['name' => 'INE', 'path' => 'documents/dummy_ine.pdf', 'type' => 'pdf'],
                ['name' => 'CURP', 'path' => 'documents/dummy_curp.pdf', 'type' => 'pdf'],
                ['name' => 'Comprobante de Domicilio', 'path' => 'documents/dummy_comprobante.jpg', 'type' => 'image'],
                ['name' => 'Constancia Laboral', 'path' => 'documents/dummy_constancia.pdf', 'type' => 'pdf'],
            ];

            foreach ($documents as $doc) {
                Documento::create([
                    'solicitud_id' => $solicitud->id,
                    'name' => $doc['name'],
                    'file_path' => $doc['path'],
                    'file_type' => $doc['type'],
                ]);
            }
        }
    }
}
