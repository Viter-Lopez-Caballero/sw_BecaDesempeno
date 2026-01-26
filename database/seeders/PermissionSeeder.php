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
            
            // Solicitudes
            'solicitudes.index' => 'Ver listado de solicitudes',
            'solicitudes.create' => 'Crear solicitudes',
            'solicitudes.edit' => 'Editar solicitudes',
            'solicitudes.delete' => 'Eliminar solicitudes',
            'solicitudes.own' => 'Ver solo solicitudes propias',
            
            // Convocatorias
            'convocatorias.index' => 'Ver listado de convocatorias',
            'convocatorias.create' => 'Crear convocatorias',
            'convocatorias.edit' => 'Editar convocatorias',
            'convocatorias.delete' => 'Eliminar convocatorias',
            
            // Evaluaciones
            'evaluaciones.index' => 'Ver listado de evaluaciones',
            'evaluaciones.create' => 'Crear evaluaciones',
            'evaluaciones.edit' => 'Editar evaluaciones',
            'evaluaciones.delete' => 'Eliminar evaluaciones',
            
            // Reconocimiento
            'reconocimiento.index' => 'Ver reconocimientos',
            'reconocimiento.create' => 'Crear reconocimientos',
            'reconocimiento.edit' => 'Editar reconocimientos',
            
            // Catálogo
            'catalogo.index' => 'Ver catálogo',
            'catalogo.create' => 'Crear en catálogo',
            'catalogo.edit' => 'Editar catálogo',
            'catalogo.delete' => 'Eliminar catálogo',
        ];

        // Crear cada permiso
        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        // Asignar permisos a roles
        $this->assignPermissionsToRoles();
    }

    private function assignPermissionsToRoles(): void
    {
        // Super Admin - TODOS los permisos
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $allPermissions = Permission::all();
            $superAdmin->syncPermissions($allPermissions);
            $this->command->info("✅ {$allPermissions->count()} permisos asignados al Super Admin.");
        }

        // Admin - Permisos de gestión (sin roles/permisos/módulos)
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $adminPermissions = Permission::whereIn('name', [
                'users.index', 'users.create', 'users.edit', 'users.delete',
                'solicitudes.index', 'solicitudes.create', 'solicitudes.edit', 'solicitudes.delete',
                'convocatorias.index', 'convocatorias.create', 'convocatorias.edit', 'convocatorias.delete',
                'reconocimiento.index', 'reconocimiento.create', 'reconocimiento.edit',
                'catalogo.index', 'catalogo.create', 'catalogo.edit', 'catalogo.delete',
            ])->get();
            $admin->syncPermissions($adminPermissions);
            $this->command->info("✅ {$adminPermissions->count()} permisos asignados al Admin.");
        }

        // Docente - Permisos de lectura y sus propias solicitudes
        $docente = Role::where('name', 'Docente')->first();
        if ($docente) {
            $docentePermissions = Permission::whereIn('name', [
                'solicitudes.own',
                'solicitudes.create',
                'solicitudes.edit',
                'convocatorias.index',
                'reconocimiento.index',
            ])->get();
            $docente->syncPermissions($docentePermissions);
            $this->command->info("✅ {$docentePermissions->count()} permisos asignados al Docente.");
        }

        // Evaluador - Permisos de evaluación y lectura
        $evaluador = Role::where('name', 'Evaluador')->first();
        if ($evaluador) {
            $evaluadorPermissions = Permission::whereIn('name', [
                'evaluaciones.index', 'evaluaciones.create', 'evaluaciones.edit',
                'convocatorias.index',
                'reconocimiento.index',
                'solicitudes.index',
            ])->get();
            $evaluador->syncPermissions($evaluadorPermissions);
            $this->command->info("✅ {$evaluadorPermissions->count()} permisos asignados al Evaluador.");
        }
    }
}
