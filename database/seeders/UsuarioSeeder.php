<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear Roles
        $superAdmin = Role::create(['name' => 'Super Admin', 'description' => 'Super Administrador del Sistema']);
        $admin = Role::create(['name' => 'Admin', 'description' => 'Administrador']);
        $docente = Role::create(['name' => 'Docente', 'description' => 'Docente Solicitante de Beca']);
        $evaluador = Role::create(['name' => 'Evaluador', 'description' => 'Evaluador de Becas']);

        // 2. Crear Usuarios de prueba
        $userSuperAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@becas.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $userSuperAdmin->assignRole($superAdmin);

        $userAdmin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@becas.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $userAdmin->assignRole($admin);

        $userDocente = User::create([
            'name' => 'Profesor Juan',
            'email' => 'juan@docente.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $userDocente->assignRole($docente);

        $userEvaluador = User::create([
            'name' => 'Evaluador Ana',
            'email' => 'ana@evaluador.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $userEvaluador->assignRole($evaluador);
    }
}
