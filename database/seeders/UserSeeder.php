<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener Roles existentes (creados por RoleSeeder)
        $superAdmin = Role::where('name', 'Super Admin')->first();
        $admin      = Role::where('name', 'Admin')->first();
        $docente    = Role::where('name', 'Docente')->first();
        $evaluador  = Role::where('name', 'Evaluador')->first();

        // ────────────────────────────────────────────────
        // Usuarios originales (sin modificar)
        // ────────────────────────────────────────────────
        $userSuperAdmin = User::create([
            'name'               => 'VITERVO LOPEZ CABALLERO',
            'email'              => 'lalo104lucky@gmail.com',
            'password'           => Hash::make('password'),
            'curp'               => 'JAFD940101HDFRRL09',
            'email_verified_at'  => now(),
        ]);
        if ($superAdmin) $userSuperAdmin->assignRole($superAdmin);

        $userAdmin = User::create([
            'name'               => 'ABRAHAM AVELINO PICHARDO',
            'email'              => 'lalo104master@gmail.com',
            'password'           => Hash::make('password'),
            'curp'               => 'PICA890101HDFRRL09',
            'email_verified_at'  => now(),
        ]);
        if ($admin) $userAdmin->assignRole($admin);

        $userDocente = User::create([
            'name'               => 'DULCE MARÍA GONZALES ARELLANO',
            'email'              => 'lalo04lucky@gmail.com',
            'password'           => Hash::make('password'),
            'curp'               => 'GAAV890101HDFRRL09',
            'institution_id'     => 1,
            'priority_area_id'   => 5,
            'sub_area_id'        => 33,
            'email_verified_at'  => now(),
        ]);
        if ($docente) $userDocente->assignRole($docente);

        $userEvaluador = User::create([
            'name'               => 'MAXIMILIANO CARRERA OROPEZA',
            'email'              => 'diego104lucky@gmail.com',
            'password'           => Hash::make('password'),
            'curp'               => 'CORM890101HDFRRL09',
            'institution_id'     => 2,
            'priority_area_id'   => 6,
            'sub_area_id'        => 37,
            'email_verified_at'  => now(),
        ]);
        if ($evaluador) $userEvaluador->assignRole($evaluador);

        // ────────────────────────────────────────────────
        // 3 Administradores adicionales
        // ────────────────────────────────────────────────
        $admins = [
            ['name' => 'CARLOS ERNESTO RAMÍREZ SOTO',    'email' => 'admin1@becas.test', 'curp' => 'RASC850312HDFMTL02'],
            ['name' => 'PATRICIA NOEMI FUENTES VEGA',     'email' => 'admin2@becas.test', 'curp' => 'FUVP920718MDFNGT05'],
            ['name' => 'ROBERTO ALEJANDRO LUNA HERRERA',  'email' => 'admin3@becas.test', 'curp' => 'LUHR880924HDFNRB07'],
        ];
        foreach ($admins as $data) {
            $u = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => Hash::make('password'),
                'curp'              => $data['curp'],
                'email_verified_at' => now(),
            ]);
            if ($admin) $u->assignRole($admin);
        }

        // ────────────────────────────────────────────────
        // 5 Evaluadores adicionales
        // ────────────────────────────────────────────────
        $evaluadores = [
            ['name' => 'JORGE ANTONIO MEDINA CASTILLO',   'email' => 'eval1@becas.test', 'curp' => 'MECJ790405HDFDSR04', 'inst' => 3,  'area' => 1, 'sub' => 4 ],
            ['name' => 'ADRIANA BEATRIZ TORRES MORALES',  'email' => 'eval2@becas.test', 'curp' => 'TOMA830917MDFRND08', 'inst' => 5,  'area' => 2, 'sub' => 12],
            ['name' => 'SAMUEL IGNACIO VEGA DOMÍNGUEZ',   'email' => 'eval3@becas.test', 'curp' => 'VEDS870623HDFGMS01', 'inst' => 7,  'area' => 3, 'sub' => 15],
            ['name' => 'LUCIA FERNANDA ROJAS ESPINOZA',   'email' => 'eval4@becas.test', 'curp' => 'ROEL910211MDFSJC06', 'inst' => 9,  'area' => 5, 'sub' => 27],
            ['name' => 'HÉCTOR MANUEL GUTIÉRREZ ÁVILA',   'email' => 'eval5@becas.test', 'curp' => 'GUAH800730HDFTVH03', 'inst' => 10, 'area' => 6, 'sub' => 36],
        ];
        foreach ($evaluadores as $data) {
            $u = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => Hash::make('password'),
                'curp'              => $data['curp'],
                'institution_id'    => $data['inst'],
                'priority_area_id'  => $data['area'],
                'sub_area_id'       => $data['sub'],
                'email_verified_at' => now(),
            ]);
            if ($evaluador) $u->assignRole($evaluador);
        }

        // ────────────────────────────────────────────────
        // 10 Docentes adicionales
        // ────────────────────────────────────────────────
        $docentes = [
            ['name' => 'MÓNICA ALEJANDRA PÉREZ RUIZ',       'email' => 'doc1@becas.test',  'curp' => 'PERM920314MDFRZN09', 'inst' => 4,  'area' => 1, 'sub' => 6 ],
            ['name' => 'FERNANDO DANIEL SALINAS ORTEGA',    'email' => 'doc2@becas.test',  'curp' => 'SAOF880510HDFRLR05', 'inst' => 6,  'area' => 2, 'sub' => 13],
            ['name' => 'ALEJANDRA CRISTINA MORA IBARRA',    'email' => 'doc3@becas.test',  'curp' => 'MOIC950128MDFRBR02', 'inst' => 8,  'area' => 3, 'sub' => 16],
            ['name' => 'RAFAEL ARMANDO SÁNCHEZ CAMPOS',     'email' => 'doc4@becas.test',  'curp' => 'SACR861202HDFNMF06', 'inst' => 11, 'area' => 4, 'sub' => 22],
            ['name' => 'DANIELA PAOLA JIMÉNEZ VARGAS',      'email' => 'doc5@becas.test',  'curp' => 'JIVD900707MDFMRN04', 'inst' => 13, 'area' => 5, 'sub' => 32],
            ['name' => 'MIGUEL ÁNGEL REYES CONTRERAS',      'email' => 'doc6@becas.test',  'curp' => 'RECM870319HDFYNM08', 'inst' => 15, 'area' => 6, 'sub' => 37],
            ['name' => 'VERÓNICA ISABEL HERNÁNDEZ LUNA',    'email' => 'doc7@becas.test',  'curp' => 'HELV930825MDFRNR03', 'inst' => 17, 'area' => 7, 'sub' => 40],
            ['name' => 'OSCAR GUILLERMO FLORES AGUILAR',    'email' => 'doc8@becas.test',  'curp' => 'FLAO891115HDFGRS07', 'inst' => 20, 'area' => 5, 'sub' => 33],
            ['name' => 'KARLA SOFÍA MENDOZA BRAVO',         'email' => 'doc9@becas.test',  'curp' => 'MEBK940601MDFNRR01', 'inst' => 22, 'area' => 7, 'sub' => 44],
            ['name' => 'ARTURO EMILIO CASTRO VILLANUEVA',   'email' => 'doc10@becas.test', 'curp' => 'CAVA850417HDFSTR09', 'inst' => 24, 'area' => 8, 'sub' => 54],
        ];
        foreach ($docentes as $data) {
            $u = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => Hash::make('password'),
                'curp'              => $data['curp'],
                'institution_id'    => $data['inst'],
                'priority_area_id'  => $data['area'],
                'sub_area_id'       => $data['sub'],
                'email_verified_at' => now(),
            ]);
            if ($docente) $u->assignRole($docente);
        }
    }
}