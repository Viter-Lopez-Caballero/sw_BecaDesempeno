<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Definir permisos para cada recurso del módulo de seguridad
        $permissions = [
            // Módulos
            'modules.index' => 'Ver listado de módulos',
            'modules.create' => 'Crear módulos',
            'modules.edit' => 'Editar módulos',
            'modules.delete' => 'Eliminar módulos',
            
            // Permisos
            'permissions.index' => 'Ver listado de permisos',
            'permissions.create' => 'Crear permisos',
            'permissions.edit' => 'Editar permisos',
            'permissions.delete' => 'Eliminar permisos',
            
            // Roles
            'roles.index' => 'Ver listado de roles',
            'roles.create' => 'Crear roles',
            'roles.edit' => 'Editar roles',
            'roles.delete' => 'Eliminar roles',
            
            // Usuarios
            'users.index' => 'Ver listado de usuarios',
            'users.create' => 'Crear usuarios',
            'users.edit' => 'Editar usuarios',
            'users.delete' => 'Eliminar usuarios',
        ];

        // Crear cada permiso
        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        // Asignar TODOS los permisos al rol Super Admin
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $allPermissions = Permission::all();
            $superAdmin->syncPermissions($allPermissions);
            $this->command->info("✅ {$allPermissions->count()} permisos asignados al Super Admin.");
        } else {
            $this->command->error('❌ No se encontró el rol Super Admin.');
        }
    }
}
