<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Definir permisos para cada recurso del módulo de seguridad
        $permissions = [
            // Seguridad (modules, permissions, roles, users)
            ['name' => 'modules.index', 'description' => 'Ver listado de módulos', 'module_key' => 'seg'],
            ['name' => 'modules.create', 'description' => 'Crear módulos', 'module_key' => 'seg'],
            ['name' => 'modules.edit', 'description' => 'Editar módulos', 'module_key' => 'seg'],
            ['name' => 'modules.delete', 'description' => 'Eliminar módulos', 'module_key' => 'seg'],
            
            ['name' => 'permissions.index', 'description' => 'Ver listado de permisos', 'module_key' => 'seg'],
            ['name' => 'permissions.create', 'description' => 'Crear permisos', 'module_key' => 'seg'],
            ['name' => 'permissions.edit', 'description' => 'Editar permisos', 'module_key' => 'seg'],
            ['name' => 'permissions.delete', 'description' => 'Eliminar permisos', 'module_key' => 'seg'],

            ['name' => 'roles.index', 'description' => 'Ver listado de roles', 'module_key' => 'seg'],
            ['name' => 'roles.create', 'description' => 'Crear roles', 'module_key' => 'seg'],
            ['name' => 'roles.edit', 'description' => 'Editar roles', 'module_key' => 'seg'],
            ['name' => 'roles.delete', 'description' => 'Eliminar roles', 'module_key' => 'seg'],

            ['name' => 'users.index', 'description' => 'Ver listado de usuarios', 'module_key' => 'seg'],
            ['name' => 'users.create', 'description' => 'Crear usuarios', 'module_key' => 'seg'],
            ['name' => 'users.edit', 'description' => 'Editar usuarios', 'module_key' => 'seg'],
            ['name' => 'users.delete', 'description' => 'Eliminar usuarios', 'module_key' => 'seg'],

            // Solicitudes
            ['name' => 'solicitudes.index', 'description' => 'Ver listado de solicitudes', 'module_key' => 'controlsoli'],
            ['name' => 'solicitudes.create', 'description' => 'Crear solicitudes', 'module_key' => 'controlsoli'],
            ['name' => 'solicitudes.edit', 'description' => 'Editar solicitudes', 'module_key' => 'controlsoli'],
            ['name' => 'solicitudes.delete', 'description' => 'Eliminar solicitudes', 'module_key' => 'controlsoli'],
            ['name' => 'solicitudes.own', 'description' => 'Ver solo solicitudes propias', 'module_key' => 'controlsoli'],

            // Convocatorias
            ['name' => 'convocatorias.index', 'description' => 'Ver listado de convocatorias', 'module_key' => 'convo'],
            ['name' => 'convocatorias.create', 'description' => 'Crear convocatorias', 'module_key' => 'convo'],
            ['name' => 'convocatorias.edit', 'description' => 'Editar convocatorias', 'module_key' => 'convo'],
            ['name' => 'convocatorias.delete', 'description' => 'Eliminar convocatorias', 'module_key' => 'convo'],

            // Evaluaciones (Asociado a controlsoli por ahora, o convo?)
            ['name' => 'evaluaciones.index', 'description' => 'Ver listado de evaluaciones', 'module_key' => 'controlsoli'],
            ['name' => 'evaluaciones.create', 'description' => 'Crear evaluaciones', 'module_key' => 'controlsoli'],
            ['name' => 'evaluaciones.edit', 'description' => 'Editar evaluaciones', 'module_key' => 'controlsoli'],
            ['name' => 'evaluaciones.delete', 'description' => 'Eliminar evaluaciones', 'module_key' => 'controlsoli'],

            // Reconocimiento (cat o convo?)
            ['name' => 'reconocimiento.index', 'description' => 'Ver reconocimientos', 'module_key' => 'convo'],
            ['name' => 'reconocimiento.create', 'description' => 'Crear reconocimientos', 'module_key' => 'convo'],
            ['name' => 'reconocimiento.edit', 'description' => 'Editar reconocimientos', 'module_key' => 'convo'],

            // Catálogo
            ['name' => 'catalogo.index', 'description' => 'Ver catálogo', 'module_key' => 'cat'],
            ['name' => 'catalogo.create', 'description' => 'Crear en catálogo', 'module_key' => 'cat'],
            ['name' => 'catalogo.edit', 'description' => 'Editar catálogo', 'module_key' => 'cat'],
            ['name' => 'catalogo.delete', 'description' => 'Eliminar catálogo', 'module_key' => 'cat'],
        ];

        // Crear cada permiso
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                [
                    'description' => $permission['description'],
                    'module_key' => $permission['module_key']
                ]
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