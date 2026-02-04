<?php

namespace Database\Seeders;

use App\Models\Renapo;
use Illuminate\Database\Seeder;

class RenapoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Pre-carga CURPs en la base de datos local para evitar consultas innecesarias a la API
     */
    public function run(): void
    {
        $renapos = [
            [
                'curp' => 'JAFD040301HMSMLGA3',
                'nombres' => 'DIEGO EDUARDO',
                'apellidoPaterno' => 'JAIMEZ',
                'apellidoMaterno' => 'FLORES',
            ],
            [
                'curp' => 'JAFM110120MMSMLRA8',
                'nombres' => 'MARIANA',
                'apellidoPaterno' => 'JAIMEZ',
                'apellidoMaterno' => 'FLORES',
            ],
        ];

        foreach ($renapos as $renapo) {
            Renapo::updateOrCreate(
                ['curp' => $renapo['curp']], // Buscar por CURP
                $renapo // Actualizar o crear con estos datos
            );
        }

        $this->command->info('✅ CURPs pre-cargados en la base de datos local (cache RENAPO)');
    }
}
