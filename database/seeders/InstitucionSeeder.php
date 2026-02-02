<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institucion;
use App\Models\Estado;

class InstitucionSeeder extends Seeder
{
    public function run(): void
    {
        $instituciones = [
            // Aguascalientes
            ['nombre' => 'Instituto Tecnológico de Aguascalientes', 'estado' => 'Aguascalientes'],
            ['nombre' => 'Instituto Tecnológico de Pabellón de Arteaga', 'estado' => 'Aguascalientes'],
            ['nombre' => 'Instituto Tecnológico de la Construcción, Campus Aguascalientes', 'estado' => 'Aguascalientes'],
            ['nombre' => 'Instituto Tecnológico de El Llano de Aguascalientes', 'estado' => 'Aguascalientes'],

            // Baja California
            ['nombre' => 'Instituto Tecnológico de Ensenada', 'estado' => 'Baja California'],
            ['nombre' => 'Instituto Tecnológico de la Construcción, Campus Baja California', 'estado' => 'Baja California'],
            ['nombre' => 'Instituto Tecnológico de Mexicali', 'estado' => 'Baja California'],
            ['nombre' => 'Instituto Tecnológico de Tijuana', 'estado' => 'Baja California'],
            ['nombre' => 'Instituto Tecnológico de la Construcción, Campus Tijuana', 'estado' => 'Baja California'],

            // Baja California Sur
            ['nombre' => 'Instituto Tecnológico de Estudios Superiores de Los Cabos', 'estado' => 'Baja California Sur'],
            ['nombre' => 'Instituto Tecnológico Superior de Cd. Constitución', 'estado' => 'Baja California Sur'],
            ['nombre' => 'Instituto Tecnológico Superior de Mulegé', 'estado' => 'Baja California Sur'],
            ['nombre' => 'Instituto Tecnológico de La Paz', 'estado' => 'Baja California Sur'],

            // Campeche
            ['nombre' => 'Instituto Tecnológico Superior de Calkiní', 'estado' => 'Campeche'],
            ['nombre' => 'Instituto Tecnológico Superior de Champotón', 'estado' => 'Campeche'],
            ['nombre' => 'Instituto Tecnológico Superior de Escárcega', 'estado' => 'Campeche'],
            ['nombre' => 'Instituto Tecnológico Superior de Hopelchén', 'estado' => 'Campeche'],
            ['nombre' => 'Instituto Tecnológico de la Construcción, Campus Campeche', 'estado' => 'Campeche'],
            ['nombre' => 'Instituto Tecnológico y de Estudios Superiores René Descartes, Plantel Campeche', 'estado' => 'Campeche'],
            ['nombre' => 'Instituto Tecnológico de Chiná', 'estado' => 'Campeche'],
            ['nombre' => 'Instituto Tecnológico de Lerma', 'estado' => 'Campeche'],
            ['nombre' => 'Instituto Tecnológico de Campeche', 'estado' => 'Campeche'],
            ['nombre' => 'Instituto Tecnológico de la Construcción, Campus Cd. del Carmen', 'estado' => 'Campeche'],

            // Chiapas
            ['nombre' => 'Instituto Tecnológico Superior de Cintalapa', 'estado' => 'Chiapas'],
            ['nombre' => 'Instituto Tecnológico de Comitán', 'estado' => 'Chiapas'],
            ['nombre' => 'Instituto Tecnológico de Frontera Comalapa', 'estado' => 'Chiapas'],
            ['nombre' => 'Instituto Tecnológico y de Estudios Superiores de Monterrey, Campus Chiapas', 'estado' => 'Chiapas'],
            ['nombre' => 'Instituto Tecnológico de Tapachula', 'estado' => 'Chiapas'],
            ['nombre' => 'Instituto Tecnológico de la Construcción, campus Chiapas', 'estado' => 'Chiapas'],
            ['nombre' => 'Instituto Tecnológico de Tuxtla Gutiérrez', 'estado' => 'Chiapas'],

            // Chihuahua
            ['nombre' => 'Instituto Tecnológico de la Construcción, A.C., Campus Chihuahua', 'estado' => 'Chihuahua'],
            ['nombre' => 'Instituto Tecnológico Superior de Nuevo Casas Grandes', 'estado' => 'Chihuahua'],
            ['nombre' => 'Instituto Tecnológico y de Estudios Superiores de Monterrey, Campus Chihuahua', 'estado' => 'Chihuahua'],
            ['nombre' => 'Instituto Tecnológico de Chihuahua', 'estado' => 'Chihuahua'],
            ['nombre' => 'Instituto Tecnológico de Chihuahua II', 'estado' => 'Chihuahua'],
            ['nombre' => 'Instituto Tecnológico de Cd. Cuauhtémoc', 'estado' => 'Chihuahua'],
            ['nombre' => 'Instituto Tecnológico de Cd. Jiménez', 'estado' => 'Chihuahua'],
            ['nombre' => 'Instituto Tecnológico y de Estudios Superiores de Monterrey, Campus Cd. Juárez', 'estado' => 'Chihuahua'],
            ['nombre' => 'Instituto Tecnológico de la Construcción, A.C., Campus Juárez', 'estado' => 'Chihuahua'],
            ['nombre' => 'Instituto Tecnológico de Cd. Juárez', 'estado' => 'Chihuahua'],
            ['nombre' => 'Instituto Tecnológico de Delicias', 'estado' => 'Chihuahua'],
            ['nombre' => 'Instituto Tecnológico de Parral', 'estado' => 'Chihuahua'],

            // Ciudad de México
            ['nombre' => 'Instituto Tecnológico de Álvaro Obregón', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico de Gustavo A. Madero', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico de Gustavo A. Madero II', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico de Iztapalapa', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico de Iztapalapa II', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico Roosevelt', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico de Iztapalapa III', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico de Milpa Alta', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico de Milpa Alta II', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico de Tláhuac', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico de Tláhuac II', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico de Tláhuac III', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico de Tlalpan', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico de Xochimilco', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico Autónomo de México (ITAM) Campus Santa Teresa', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico de la Construcción', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico y de Estudios Superiores de Monterrey, Campus Santa Fé', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico Autónomo de México (ITAM)', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico y de Estudios Superiores de Monterrey', 'estado' => 'Ciudad de México'],
            ['nombre' => 'Instituto Tecnológico de Teléfonos de México', 'estado' => 'Ciudad de México'],


            // Coahuila
            ['nombre' => 'Instituto Tecnológico Superior de Cd. Acuña', 'estado' => 'Coahuila'],
            ['nombre' => 'Instituto Tecnológico Superior de Monclova', 'estado' => 'Coahuila'],
            ['nombre' => 'Instituto Tecnológico Superior de Múzquiz', 'estado' => 'Coahuila'],
            ['nombre' => 'Instituto Tecnológico Superior de San Pedro de las Colonias', 'estado' => 'Coahuila'],
            ['nombre' => 'Instituto Tecnológico de Torreón', 'estado' => 'Coahuila'],
            ['nombre' => 'Instituto Tecnológico Agropecuario Nº 10 de Torreón', 'estado' => 'Coahuila'],
            ['nombre' => 'Instituto Tecnológico de La Laguna', 'estado' => 'Coahuila'],
            ['nombre' => 'Instituto Tecnológico y de Estudios Superiores de Monterrey, Campus Laguna', 'estado' => 'Coahuila'],
            ['nombre' => 'Instituto Tecnológico de Piedras Negras', 'estado' => 'Coahuila'],
            ['nombre' => 'Instituto Tecnológico de Saltillo', 'estado' => 'Coahuila'],
            ['nombre' => 'Instituto Tecnológico Superior de Ramos Arizpe', 'estado' => 'Coahuila'],
            ['nombre' => 'Instituto Tecnológico y de Estudios Superiores de Monterrey, Campus Saltillo', 'estado' => 'Coahuila'],

            // Colima
            ['nombre' => 'Instituto Tecnológico Superior de Tamazula', 'estado' => 'Colima'],
            ['nombre' => 'Instituto Tecnológico de Colima', 'estado' => 'Colima'],

            // Durango
            ['nombre' => 'Instituto Tecnológico Superior de La Región de los Llanos', 'estado' => 'Durango'],
            ['nombre' => 'Instituto Tecnológico Superior de Lerdo', 'estado' => 'Durango'],
            ['nombre' => 'Instituto Tecnológico Superior de Santa María del Oro', 'estado' => 'Durango'],
            ['nombre' => 'Instituto Tecnológico Superior de Santiago Papasquiaro', 'estado' => 'Durango'],
            ['nombre' => 'Instituto Tecnológico de El Salto', 'estado' => 'Durango'],
            ['nombre' => 'Instituto Tecnológico de Durango', 'estado' => 'Durango'],
            ['nombre' => 'Instituto Tecnológico del Valle del Guadiana', 'estado' => 'Durango'],

            // Estado de México
            ['nombre' => 'Tecnológico de Estudios Superiores de Chalco', 'estado' => 'Estado de México'],
            ['nombre' => 'Tecnológico de Estudios Superiores de Chicoloapan', 'estado' => 'Estado de México'],
            ['nombre' => 'Tecnológico de Estudios Superiores de Chimalhuacán', 'estado' => 'Estado de México'],
            ['nombre' => 'Tecnológico de Estudios Superiores de Coacalco', 'estado' => 'Estado de México'],
            ['nombre' => 'Tecnológico de Estudios Superiores de Cuautitlán Izcalli', 'estado' => 'Estado de México'],
            ['nombre' => 'Tecnológico de Estudios Superiores de Ecatepec', 'estado' => 'Estado de México'],
            ['nombre' => 'Tecnológico de Estudios Superiores de Huixquilucan', 'estado' => 'Estado de México'],
            ['nombre' => 'Tecnológico de Estudios Superiores de Ixtapaluca', 'estado' => 'Estado de México'],
            ['nombre' => 'Tecnológico de Estudios Superiores de Jilotepec', 'estado' => 'Estado de México'],
            ['nombre' => 'Tecnológico de Estudios Superiores de Jocotitlán', 'estado' => 'Estado de México'],
            ['nombre' => 'Tecnológico de Estudios Superiores de Oriente del Estado de México', 'estado' => 'Estado de México'],
            ['nombre' => 'Tecnológico de Estudios Superiores de San Felipe del Progreso', 'estado' => 'Estado de México'],
            ['nombre' => 'Tecnológico de Estudios Superiores de Tianguistenco', 'estado' => 'Estado de México'],
            ['nombre' => 'Tecnológico de Estudios Superiores de Valle de Bravo', 'estado' => 'Estado de México'],
            ['nombre' => 'Tecnológico de Estudios Superiores de Villa Guerrero', 'estado' => 'Estado de México'],
            ['nombre' => 'Instituto Tecnológico de Atlacomulco', 'estado' => 'Estado de México'],
            ['nombre' => 'Instituto Tecnológico de Tlalnepantla', 'estado' => 'Estado de México'],
            ['nombre' => 'Instituto Tecnológico de Toluca', 'estado' => 'Estado de México'],

            // Guanajuato
            ['nombre' => 'Instituto Tecnológico Superior de Abasolo', 'estado' => 'Guanajuato'],
            ['nombre' => 'Instituto Tecnológico Superior de Guanajuato', 'estado' => 'Guanajuato'],
            ['nombre' => 'Instituto Tecnológico Nacional de Celaya, Campus I', 'estado' => 'Guanajuato'],
            ['nombre' => 'Instituto Tecnológico Superior de Irapuato', 'estado' => 'Guanajuato'],
            ['nombre' => 'Instituto Tecnológico Superior de Purísima Del Rincón', 'estado' => 'Guanajuato'],
            ['nombre' => 'Instituto Tecnológico Superior de Salvatierra', 'estado' => 'Guanajuato'],
            ['nombre' => 'Instituto Tecnológico Superior de Sur de Guanajuato', 'estado' => 'Guanajuato'],
            ['nombre' => 'Instituto Tecnológico de Roque', 'estado' => 'Guanajuato'],
            ['nombre' => 'Instituto Tecnológico de Celaya', 'estado' => 'Guanajuato'],
            ['nombre' => 'Instituto Tecnológico de León', 'estado' => 'Guanajuato'],
            ['nombre' => 'Instituto Tecnológico de Diseño de Modas', 'estado' => 'Guanajuato'],

            // Guerrero
            ['nombre' => 'Instituto Tecnológico Superior de La Costa Chica', 'estado' => 'Guerrero'],
            ['nombre' => 'Instituto Tecnológico Superior de La Montaña', 'estado' => 'Guerrero'],
            ['nombre' => 'Instituto Tecnológico de Acapulco', 'estado' => 'Guerrero'],
            ['nombre' => 'Instituto Tecnológico de Chilpancingo', 'estado' => 'Guerrero'],
            ['nombre' => 'Instituto Tecnológico de Iguala', 'estado' => 'Guerrero'],
            ['nombre' => 'Instituto Tecnológico de San Marcos', 'estado' => 'Guerrero'],
            ['nombre' => 'Instituto Tecnológico de Ciudad Altamirano', 'estado' => 'Guerrero'],
            ['nombre' => 'Instituto Tecnológico de la Costa Grande', 'estado' => 'Guerrero'],
            

            // Hidalgo
            ['nombre' => 'Instituto Tecnológico Superior del Occidente del Estado de Hidalgo', 'estado' => 'Hidalgo'],
            ['nombre' => 'Instituto Tecnológico Superior del Oriente del Estado de Hidalgo', 'estado' => 'Hidalgo'],
            ['nombre' => 'Instituto Tecnológico Superior de Huichapan', 'estado' => 'Hidalgo'],
            ['nombre' => 'Instituto Tecnológico de Huejutla', 'estado' => 'Hidalgo'],
            ['nombre' => 'Instituto Tecnológico de Atitalaquia', 'estado' => 'Hidalgo'],
            ['nombre' => 'Instituto Tecnológico de Pachuca', 'estado' => 'Hidalgo'],
            ['nombre' => 'Instituto Tecnológico de Tula de Allende', 'estado' => 'Hidalgo'],
            ['nombre' => 'Instituto Tecnológico Latinoamericano, Campus Central', 'estado' => 'Hidalgo'],
            ['nombre' => 'Instituto Tecnológico de la Construcción (ITC), Campus Hidalgo', 'estado' => 'Hidalgo'],
            ['nombre' => 'Instituto Tecnológico Latinoamericano, Campus Tula', 'estado' => 'Hidalgo'],

            // Jalisco
            ['nombre' => 'Instituto Tecnológico Superior de Chapala', 'estado' => 'Jalisco'],
            ['nombre' => 'Instituto Tecnológico Superior de Cocula', 'estado' => 'Jalisco'],
            ['nombre' => 'Instituto Tecnológico Superior de Mascota', 'estado' => 'Jalisco'],
            ['nombre' => 'Instituto Tecnológico Superior José Mario Molina Pasquel y Henríquez', 'estado' => 'Jalisco'],
            ['nombre' => 'Instituto Tecnológico Superior de Arandas', 'estado' => 'Jalisco'],
            ['nombre' => 'Instituto Tecnológico de Ciudad Guzmán', 'estado' => 'Jalisco'],
            ['nombre' => 'Instituto Tecnológico Superior de la Huerta', 'estado' => 'Jalisco'],
            ['nombre' => 'Instituto Tecnológico Superior de El Grullo', 'estado' => 'Jalisco'],
            ['nombre' => 'Instituto Tecnológico de Tlajomulco', 'estado' => 'Jalisco'],
            ['nombre' => 'Instituto Tecnológico y de Estudios Superiores de Occidente', 'estado' => 'Jalisco'],
            ['nombre' => 'Instituto Tecnológico de Ocotlán', 'estado' => 'Jalisco'],
            ['nombre' => 'Instituto Tecnológico Superior de Zapotlanejo', 'estado' => 'Jalisco'],
            ['nombre' => 'Instituto Tecnológico Superior de Zapopan', 'estado' => 'Jalisco'],
            ['nombre' => 'Instituto Tecnológico José Mario Molina Pasquel y Enríquez - Lagos de Moreno', 'estado' => 'Jalisco'],
            ['nombre' => 'Instituto Tecnológico Superior de Puerto Vallarta', 'estado' => 'Jalisco'],

            // Michoacán
            ['nombre' => 'Instituto Tecnológico de Estudios Superiores de Zamora', 'estado' => 'Michoacán'],
            ['nombre' => 'Instituto Tecnológico Superior de Apatzingán', 'estado' => 'Michoacán'],
            ['nombre' => 'Instituto Tecnológico Superior de Cd. Hidalgo', 'estado' => 'Michoacán'],
            ['nombre' => 'Instituto Tecnológico Superior de Coalcomán', 'estado' => 'Michoacán'],
            ['nombre' => 'Instituto Tecnológico Superior de Huetamo', 'estado' => 'Michoacán'],
            ['nombre' => 'Instituto Tecnológico Superior de Los Reyes', 'estado' => 'Michoacán'],
            ['nombre' => 'Instituto Tecnológico Superior de Pátzcuaro', 'estado' => 'Michoacán'],
            ['nombre' => "Instituto Tecnológico Superior de P'urhépecha", 'estado' => 'Michoacán'],
            ['nombre' => 'Instituto Tecnológico Superior de Puruándiro', 'estado' => 'Michoacán'],
            ['nombre' => 'Instituto Tecnológico Superior de Tacámbaro', 'estado' => 'Michoacán'],
            ['nombre' => 'Instituto Tecnológico Superior de Uruapan', 'estado' => 'Michoacán'],
            ['nombre' => 'Instituto Tecnológico de Jiquilpan', 'estado' => 'Michoacán'],
            ['nombre' => 'Instituto Tecnológico de La Piedad', 'estado' => 'Michoacán'],
            ['nombre' => 'Instituto Tecnológico de Lázaro Cárdenas', 'estado' => 'Michoacán'],
            ['nombre' => 'Instituto Tecnológico de Morelia', 'estado' => 'Michoacán'],
            ['nombre' => 'Instituto Tecnológico y de Estudios Superiores de Monterrey, Campus Morelia', 'estado' => 'Michoacán'],
            ['nombre' => 'Instituto Tecnológico de Zitácuaro', 'estado' => 'Michoacán'],

            // Morelos
            ['nombre' => 'Centro Nacional de Investigación y Desarrollo Tecnológico', 'estado' => 'Morelos'],
            ['nombre' => 'Instituto Tecnológico de Cuautla', 'estado' => 'Morelos'],
            ['nombre' => 'Instituto Tecnológico de Zacatepec', 'estado' => 'Morelos'],
            ['nombre' => 'Instituto Tecnológico del Valle de Oaxaca', 'estado' => 'Morelos'],
            ['nombre' => 'Instituto Tecnológico de la Construcción (ITC), Campus Cuernavaca', 'estado' => 'Morelos'],

            // Nayarit
            ['nombre' => 'Instituto Tecnológico de Bahía de Banderas', 'estado' => 'Nayarit'],
            ['nombre' => 'Instituto Tecnológico de Estudios Superiores de Nayarit', 'estado' => 'Nayarit'],
            ['nombre' => 'Instituto Tecnológico del Norte de Nayarit', 'estado' => 'Nayarit'],
            ['nombre' => 'Instituto Tecnológico del Sur de Nayarit', 'estado' => 'Nayarit'],
            ['nombre' => 'Instituto Tecnológico de Tepic', 'estado' => 'Nayarit'],
            ['nombre' => 'Instituto Tecnológico de la Construcción (ITC), Plantel Tepic', 'estado' => 'Nayarit'],

            // Nuevo León
            ['nombre' => 'Instituto Tecnológico Superior de Montemorelos', 'estado' => 'Nuevo León'],
            ['nombre' => 'Instituto Tecnológico de Linares', 'estado' => 'Nuevo León'],
            ['nombre' => 'Instituto Tecnológico de Nuevo León', 'estado' => 'Nuevo León'],

            // Oaxaca
            ['nombre' => 'Instituto Tecnológico Superior de San Miguel el Grande', 'estado' => 'Oaxaca'],
            ['nombre' => 'Instituto Tecnológico Superior de Teposcolula', 'estado' => 'Oaxaca'],
            ['nombre' => 'Instituto Tecnológico Superior de Huatulco', 'estado' => 'Oaxaca'],
            ['nombre' => 'Instituto Tecnológico del Valle de Etla', 'estado' => 'Oaxaca'],
            ['nombre' => 'Instituto Tecnológico del Valle de Oaxaca', 'estado' => 'Oaxaca'],
            ['nombre' => 'Instituto Tecnológico del Istmo', 'estado' => 'Oaxaca'],
            ['nombre' => 'Instituto Tecnológico de la Construcción (ITC), Plantel Oaxaca', 'estado' => 'Oaxaca'],
            ['nombre' => 'Instituto Tecnológico de La Cuenca del Papaloapan', 'estado' => 'Oaxaca'],
            ['nombre' => 'Instituto Tecnológico de Salina Cruz', 'estado' => 'Oaxaca'],
            ['nombre' => 'Instituto Tecnológico de Comitancillo', 'estado' => 'Oaxaca'],
            ['nombre' => 'Instituto Tecnológico de Oaxaca', 'estado' => 'Oaxaca'],
            ['nombre' => 'Instituto Tecnológico de Pinotepa', 'estado' => 'Oaxaca'],
            ['nombre' => 'Instituto Tecnológico de Pochutla', 'estado' => 'Oaxaca'],
            ['nombre' => 'Instituto Tecnológico de Tlaxiaco', 'estado' => 'Oaxaca'],
            ['nombre' => 'Instituto Tecnológico Agropecuario no. 3 de Tuxtepec', 'estado' => 'Oaxaca'],
            ['nombre' => 'Instituto Tecnológico de Tuxtepec', 'estado' => 'Oaxaca'],

            // Puebla
            ['nombre' => 'Instituto Tecnológico Superior de Acatlán de Osorio', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico Superior de Atlixco', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico Superior de la Sierra Norte de Puebla', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico Superior de La Sierra Negra de Ajalpan', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico Superior de Cd. Serdán', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico Superior de Huauchinango', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico Superior de Libres', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico Superior de San Martín Texmelucan', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico Superior de Tepeaca', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico Superior de Tepexi de Rodríguez', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico Superior de Teziutlán', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico Superior de Tlatlauquitepec', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico Superior de Venustiano Carranza', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico Superior de Zacapoaxtla', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico Superior de Zacatlán', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico de Tecomatlán', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico de Izúcar de Matamoros', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico de Puebla', 'estado' => 'Puebla'],
            ['nombre' => 'Instituto Tecnológico de Tehuacán', 'estado' => 'Puebla'],

            // Querétaro
            ['nombre' => 'Centro Interdisciplinario de Investigación y Docencia en Educación Técnica', 'estado' => 'Querétaro'],
            ['nombre' => 'Instituto Tecnológico de la Construcción, Campus Querétaro', 'estado' => 'Querétaro'],
            ['nombre' => 'Instituto Tecnológico de la Construccion, Plantel Querétaro', 'estado' => 'Querétaro'],
            ['nombre' => 'Instituto Tecnológico de Querétaro', 'estado' => 'Querétaro'],
            ['nombre' => 'Instituto Tecnológico de San Juan del Río', 'estado' => 'Querétaro'],

            // Quintana Roo
            ['nombre' => 'Instituto Tecnológico Superior de Tulum', 'estado' => 'Quintana Roo'],
            ['nombre' => 'Instituto Tecnológico Superior de Felipe Carrillo Puerto', 'estado' => 'Quintana Roo'],
            ['nombre' => 'Instituto Tecnológico de La Zona Maya', 'estado' => 'Quintana Roo'],
            ['nombre' => 'Instituto Tecnológico de Cancún', 'estado' => 'Quintana Roo'],
            ['nombre' => 'Instituto Tecnológico de Chetumal', 'estado' => 'Quintana Roo'],

            // San Luis Potosí
            ['nombre' => 'Instituto Tecnológico Superior de Ébano', 'estado' => 'San Luis Potosí'],
            ['nombre' => 'Instituto Tecnológico Superior de Rioverde', 'estado' => 'San Luis Potosí'],
            ['nombre' => 'Instituto Tecnológico Superior de Tamazunchale', 'estado' => 'San Luis Potosí'],
            ['nombre' => 'Instituto Tecnológico de Matehuala', 'estado' => 'San Luis Potosí'],
            ['nombre' => 'Instituto Tecnológico y de Estudios Superiores de Monterrey (ITESM). Campus San Luis Potosí', 'estado' => 'San Luis Potosí'],
            ['nombre' => 'Instituto Tecnológico Superior de San Luis Potosí Capital', 'estado' => 'San Luis Potosí'],
            ['nombre' => 'Instituto Tecnológico de San Luis Potosí', 'estado' => 'San Luis Potosí'],
            ['nombre' => 'Instituto Tecnológico de Ciudad Valles', 'estado' => 'San Luis Potosí'],

            // Sinaloa
            ['nombre' => 'Instituto Tecnológico Superior de Guasave', 'estado' => 'Sinaloa'],
            ['nombre' => 'Instituto Tecnológico de Mazatlán', 'estado' => 'Sinaloa'],
            ['nombre' => 'Instituto Tecnológico de Culiacán', 'estado' => 'Sinaloa'],
            ['nombre' => 'Instituto Tecnológico Superior de El Dorado', 'estado' => 'Sinaloa'],
            ['nombre' => 'Instituto Tecnológico Superior de los Mochis', 'estado' => 'Sinaloa'],
            ['nombre' => 'Instituto Tecnológico Superior de Sinaloa, Campus Centro', 'estado' => 'Sinaloa'],
            ['nombre' => 'Instituto Tecnológico de Los Mochis', 'estado' => 'Sinaloa'],
            ['nombre' => 'Instituto Tecnológico de la Construcción (ITC), Delegación Sinaloa Centro', 'estado' => 'Sinaloa'],
            ['nombre' => 'Instituto Tecnológico de Sinaloa de Leyva', 'estado' => 'Sinaloa'],
            ['nombre' => 'Instituto Tecnológico de la Construcción (ITC), Delegación Sinaloa Norte', 'estado' => 'Sinaloa'],

            // Sonora
            ['nombre' => 'Instituto Tecnológico Superior de Cajeme', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico de Sonora (ITSON), Campus Obregón Centro', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico de Sonora (ITSON), Campus Obregón Náinari', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico de Sonora (ITSON), Campus Empalme', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico Superior de Cananea', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico del Valle del Yaqui', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico Superior de Puerto Peñasco', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico de Guaymas', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico de Sonora (ITSON), Campus Guaymas', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico y de Estudios Superiores de Monterrey (ITESM), Campus Sonora Norte', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico de la Construcción (ITC), Delegación Sonora', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico de Sonora (ITSON), Campus Navojoa Sur', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico de Sonora (ITSON), Campus Navojoa Centro', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico de Agua Prieta', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico de Hermosillo', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico de Huatabampo', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico de Nogales', 'estado' => 'Sonora'],
            ['nombre' => 'Instituto Tecnológico de San Luis Río Colorado', 'estado' => 'Sonora'],

            // Tabasco
            ['nombre' => 'Instituto Tecnológico Superior de Centla', 'estado' => 'Tabasco'],
            ['nombre' => 'Instituto Tecnológico Superior de Comalcalco', 'estado' => 'Tabasco'],
            ['nombre' => 'Instituto Tecnológico Superior de los Ríos', 'estado' => 'Tabasco'],
            ['nombre' => 'Instituto Tecnológico Superior de Macuspana', 'estado' => 'Tabasco'],
            ['nombre' => 'Instituto Tecnológico Superior de Villa La Venta', 'estado' => 'Tabasco'],
            ['nombre' => 'Instituto Tecnológico de La Zona Olmeca', 'estado' => 'Tabasco'],
            ['nombre' => 'Instituto Tecnológico de Huimanguillo', 'estado' => 'Tabasco'],
            ['nombre' => 'Instituto Tecnológico de la Construcción (ITC), campus Tabasco', 'estado' => 'Tabasco'],
            ['nombre' => 'Instituto Tecnológico de La Chontalpa', 'estado' => 'Tabasco'],
            ['nombre' => 'Instituto Tecnológico Superior de la Región Sierra', 'estado' => 'Tabasco'],
            ['nombre' => 'Instituto Tecnológico de Villahermosa', 'estado' => 'Tabasco'],

            // Tamaulipas
            ['nombre' => 'Instituto Tecnológico Superior de San Fernando', 'estado' => 'Tamaulipas'],
            ['nombre' => 'Instituto Tecnológico Superior de Mante', 'estado' => 'Tamaulipas'],
            ['nombre' => 'Instituto Tecnológico de Altamira', 'estado' => 'Tamaulipas'],
            ['nombre' => 'Instituto Tecnológico de Cd. Madero', 'estado' => 'Tamaulipas'],
            ['nombre' => 'Instituto Tecnológico de Cd. Victoria', 'estado' => 'Tamaulipas'],
            ['nombre' => 'Instituto Tecnológico de Matamoros', 'estado' => 'Tamaulipas'],
            ['nombre' => 'Instituto Tecnológico de Nuevo Laredo', 'estado' => 'Tamaulipas'],
            ['nombre' => 'Instituto Tecnológico de la Construcción (ITC), Delegación Tamaulipas', 'estado' => 'Tamaulipas'],
            ['nombre' => 'Instituto Tecnológico de Reynosa', 'estado' => 'Tamaulipas'],

            // Tlaxcala
            ['nombre' => 'Instituto Tecnológico Superior de Tlaxco', 'estado' => 'Tlaxcala'],
            ['nombre' => 'Instituto Tecnológico de Altiplano de Tlaxcala', 'estado' => 'Tlaxcala'],
            ['nombre' => 'Instituto Tecnológico de Tlaxcala', 'estado' => 'Tlaxcala'],
            ['nombre' => 'Instituto Tecnológico de Apizaco', 'estado' => 'Tlaxcala'],

            // Veracruz
            ['nombre' => 'Instituto Tecnológico Superior de Acayucan', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Álamo Temapache', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Alvarado', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Chicontepec', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Zongolica (ITS), Sede Tequila', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Zongolica (ITS), Sede Tezonapa', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Coatzacoalcos', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Cosamaloapan', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico de la Construcción (ITC)', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Huatusco', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Jesús Carranza', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Juan Rodríguez Clara', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Las Choapas', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico de la Construcción (ITC), Delegación Veracruz Norte', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico de la Construcción, Delegación Veracruz Sur', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico de la Construcción (ITC), Delegación Veracruz Centro', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Martínez de la Torre', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Misantla', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Naranjos', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Pánuco', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Perote', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Poza Rica', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de San Andrés Tuxtla', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Tantoyuca', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Tierra Blanca', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Xalapa', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Zongolica', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Zongolica (ITS), Sede Nogales', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Zongolica (ITS), Sede Tehuipango', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico de Úrsulo Galván', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico de Boca del Río', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico de Cerro Azul', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico de Minatitlán', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico de Orizaba', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico de Informática y Administración', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico de Veracruz', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico Superior de Zozocolco de Hidalgo', 'estado' => 'Veracruz'],
            ['nombre' => 'Instituto Tecnológico de Estudios Superiores René Descartes, Plantel Veracruz', 'estado' => 'Veracruz'],

            // Yucatán
            ['nombre' => 'Instituto Tecnológico Superior de Motul', 'estado' => 'Yucatán'],
            ['nombre' => 'Instituto Tecnológico Superior de Progreso', 'estado' => 'Yucatán'],
            ['nombre' => 'Instituto Tecnológico de la Construcción (ITC), Delegación Yucatán', 'estado' => 'Yucatán'],
            ['nombre' => 'Instituto Tecnológico Superior de Sur del Estado de Yucatán', 'estado' => 'Yucatán'],
            ['nombre' => 'Instituto Tecnológico Superior de Valladolid', 'estado' => 'Yucatán'],
            ['nombre' => 'Instituto Tecnológico de Conkal', 'estado' => 'Yucatán'],
            ['nombre' => 'Instituto Tecnológico de Tizimín', 'estado' => 'Yucatán'],
            ['nombre' => 'Instituto Tecnológico de Mérida', 'estado' => 'Yucatán'],

            // Zacatecas
            ['nombre' => 'Instituto Tecnológico Superior de Fresnillo', 'estado' => 'Zacatecas'],
            ['nombre' => 'Instituto Tecnológico Superior de Jerez', 'estado' => 'Zacatecas'],
            ['nombre' => 'Instituto Tecnológico Superior de Loreto', 'estado' => 'Zacatecas'],
            ['nombre' => 'Instituto Tecnológico Superior de Nochistlán', 'estado' => 'Zacatecas'],
            ['nombre' => 'Instituto Tecnológico Superior de Zacatecas Norte', 'estado' => 'Zacatecas'],
            ['nombre' => 'Instituto Tecnológico Superior de Zacatecas Occidente', 'estado' => 'Zacatecas'],
            ['nombre' => 'Instituto Tecnológico de la Construcción (ITC), Delegación Zacatecas', 'estado' => 'Zacatecas'],
            ['nombre' => 'Instituto Tecnológico Superior de Zacatecas Sur', 'estado' => 'Zacatecas'],
            ['nombre' => 'Instituto Tecnológico de Zacatecas', 'estado' => 'Zacatecas'],
        ];

        foreach ($instituciones as $inst) {
            $estado = Estado::where('nombre', $inst['estado'])->first();

            if ($estado) {
                Institucion::firstOrCreate([
                    'nombre' => $inst['nombre'],
                    'estado_id' => $estado->id,
                ]);
            }
        }
    }
}
