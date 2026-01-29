<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Estado;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            ['nombre' => 'Aguascalientes', 'abreviatura' => 'AGS'],
            ['nombre' => 'Baja California', 'abreviatura' => 'BC'],
            ['nombre' => 'Baja California Sur', 'abreviatura' => 'BCS'],
            ['nombre' => 'Campeche', 'abreviatura' => 'CAMP'],
            ['nombre' => 'Coahuila', 'abreviatura' => 'COAH'],
            ['nombre' => 'Colima', 'abreviatura' => 'COL'],
            ['nombre' => 'Chiapas', 'abreviatura' => 'CHIS'],
            ['nombre' => 'Chihuahua', 'abreviatura' => 'CHIH'],
            ['nombre' => 'Ciudad de México', 'abreviatura' => 'CDMX'],
            ['nombre' => 'Durango', 'abreviatura' => 'DGO'],
            ['nombre' => 'Guanajuato', 'abreviatura' => 'GTO'],
            ['nombre' => 'Guerrero', 'abreviatura' => 'GRO'],
            ['nombre' => 'Hidalgo', 'abreviatura' => 'HGO'],
            ['nombre' => 'Jalisco', 'abreviatura' => 'JAL'],
            ['nombre' => 'México', 'abreviatura' => 'MEX'],
            ['nombre' => 'Michoacán', 'abreviatura' => 'MICH'],
            ['nombre' => 'Morelos', 'abreviatura' => 'MOR'],
            ['nombre' => 'Nayarit', 'abreviatura' => 'NAY'],
            ['nombre' => 'Nuevo León', 'abreviatura' => 'NL'],
            ['nombre' => 'Oaxaca', 'abreviatura' => 'OAX'],
            ['nombre' => 'Puebla', 'abreviatura' => 'PUE'],
            ['nombre' => 'Querétaro', 'abreviatura' => 'QRO'],
            ['nombre' => 'Quintana Roo', 'abreviatura' => 'QROO'],
            ['nombre' => 'San Luis Potosí', 'abreviatura' => 'SLP'],
            ['nombre' => 'Sinaloa', 'abreviatura' => 'SIN'],
            ['nombre' => 'Sonora', 'abreviatura' => 'SON'],
            ['nombre' => 'Tabasco', 'abreviatura' => 'TAB'],
            ['nombre' => 'Tamaulipas', 'abreviatura' => 'TAMPS'],
            ['nombre' => 'Tlaxcala', 'abreviatura' => 'TLAX'],
            ['nombre' => 'Veracruz', 'abreviatura' => 'VER'],
            ['nombre' => 'Yucatán', 'abreviatura' => 'YUC'],
            ['nombre' => 'Zacatecas', 'abreviatura' => 'ZAC'],
        ];

        foreach ($estados as $estado) {
            Estado::firstOrCreate(['nombre' => $estado['nombre']], $estado);
        }
    }
}
