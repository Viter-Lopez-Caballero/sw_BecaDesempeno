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
                'curp' => 'JXSI331110HCMMEC58',
                'nombres' => 'ABRAHAM',
                'apellidoPaterno' => 'AVELINO',
                'apellidoMaterno' => 'PICHARDO',
            ],
            [
                'curp' => 'GORJ990215MMSLNA05',
                'nombres' => 'JANETH',
                'apellidoPaterno' => 'GONZALEZ',
                'apellidoMaterno' => 'RIOS',
            ],
            [
                'curp' => 'HESM010823HMSNRR09',
                'nombres' => 'MARIO ALEJANDRO',
                'apellidoPaterno' => 'HERNANDEZ',
                'apellidoMaterno' => 'SERRANO',
            ],
            [
                'curp' => 'LUPG030714MMSZNL04',
                'nombres' => 'GLORIA ITZEL',
                'apellidoPaterno' => 'LUNA',
                'apellidoMaterno' => 'POZOS',
            ],
            [
                'curp' => 'MARM971125HDFNRG07',
                'nombres' => 'RODRIGO',
                'apellidoPaterno' => 'MARTINEZ',
                'apellidoMaterno' => 'ROMAN',
            ],
            [
                'curp' => 'VACE050602MMSNRS02',
                'nombres' => 'ESTEFANIA',
                'apellidoPaterno' => 'VARGAS',
                'apellidoMaterno' => 'CERVANTES',
            ],
            [
                'curp' => 'TOZL880930HMSRPL06',
                'nombres' => 'LUIS ENRIQUE',
                'apellidoPaterno' => 'TORRES',
                'apellidoMaterno' => 'ZAPATA',
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
