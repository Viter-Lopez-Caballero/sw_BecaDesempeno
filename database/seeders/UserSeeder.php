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
        $admin = Role::where('name', 'Admin')->first();
        $docente = Role::where('name', 'Docente')->first();
        $evaluador = Role::where('name', 'Evaluador')->first();

        // 2. Crear Usuarios de prueba
        $userSuperAdmin = User::create([
            'name' => 'Dr. Diego Eduardo Jaimez Flores',
            'email' => 'lalo104lucky@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        if ($superAdmin)
            $userSuperAdmin->assignRole($superAdmin);

        $userAdmin = User::create([
            'name' => 'Dr. Abraham Avelino Pichardo',
            'email' => 'lalo104master@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        if ($admin)
            $userAdmin->assignRole($admin);

        $userDocente = User::create([
            'name' => 'Mta. Dulce María Gonzales Arellano',
            'email' => 'lalo04lucky@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        if ($docente)
            $userDocente->assignRole($docente);

        $userEvaluador = User::create([
            'name' => 'Dr. Maximiliano Carrera Oropeza',
            'email' => 'diego104lucky@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        if ($evaluador)
            $userEvaluador->assignRole($evaluador);
    }
}