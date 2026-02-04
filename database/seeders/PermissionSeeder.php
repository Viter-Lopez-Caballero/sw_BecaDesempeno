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

            // Solicitudes (Admin - Gestión Individual)
            ['name' => 'solicitudes.index', 'description' => 'Ver listado de solicitudes', 'module_key' => 'adminsoli'],
            ['name' => 'solicitudes.create', 'description' => 'Crear solicitudes', 'module_key' => 'adminsoli'],
            ['name' => 'solicitudes.edit', 'description' => 'Editar solicitudes', 'module_key' => 'adminsoli'],
            ['name' => 'solicitudes.delete', 'description' => 'Eliminar solicitudes', 'module_key' => 'adminsoli'],
            ['name' => 'solicitudes.own', 'description' => 'Ver solo solicitudes propias', 'module_key' => 'adminsoli'],
            ['name' => 'solicitudes.assign', 'description' => 'Asignar evaluadores a solicitudes', 'module_key' => 'adminsoli'],
            ['name' => 'solicitudes.verdict', 'description' => 'Emitir veredicto final en solicitudes', 'module_key' => 'adminsoli'],

            // Convocatorias
            ['name' => 'convocatorias.index', 'description' => 'Ver listado de convocatorias', 'module_key' => 'convo'],
            ['name' => 'convocatorias.create', 'description' => 'Crear convocatorias', 'module_key' => 'convo'],
            ['name' => 'convocatorias.edit', 'description' => 'Editar convocatorias', 'module_key' => 'convo'],
            ['name' => 'convocatorias.delete', 'description' => 'Eliminar convocatorias', 'module_key' => 'convo'],

            // Evaluaciones (Asociado a adminsoli)
            ['name' => 'evaluaciones.index', 'description' => 'Ver listado de evaluaciones', 'module_key' => 'adminsoli'],
            ['name' => 'evaluaciones.create', 'description' => 'Crear evaluaciones', 'module_key' => 'adminsoli'],
            ['name' => 'evaluaciones.edit', 'description' => 'Editar evaluaciones', 'module_key' => 'adminsoli'],
            ['name' => 'evaluaciones.delete', 'description' => 'Eliminar evaluaciones', 'module_key' => 'adminsoli'],

            // Reconocimiento
            ['name' => 'reconocimiento.index', 'description' => 'Ver reconocimientos', 'module_key' => 'reconocimiento'],
            ['name' => 'reconocimiento.create', 'description' => 'Crear reconocimientos', 'module_key' => 'reconocimiento'],
            ['name' => 'reconocimiento.edit', 'description' => 'Editar reconocimientos', 'module_key' => 'reconocimiento'],
            ['name' => 'reconocimiento.toggle', 'description' => 'Activar/Desactivar reconocimientos', 'module_key' => 'reconocimiento'],

            // Catálogo
            ['name' => 'catalogo.index', 'description' => 'Ver catálogo', 'module_key' => 'cat'],
            ['name' => 'catalogo.create', 'description' => 'Crear en catálogo', 'module_key' => 'cat'],
            ['name' => 'catalogo.edit', 'description' => 'Editar catálogo', 'module_key' => 'cat'],
            ['name' => 'catalogo.delete', 'description' => 'Eliminar catálogo', 'module_key' => 'cat'],

            // Instituciones
            ['name' => 'instituciones.index', 'description' => 'Ver instituciones', 'module_key' => 'cat'],
            ['name' => 'instituciones.create', 'description' => 'Crear instituciones', 'module_key' => 'cat'],
            ['name' => 'instituciones.edit', 'description' => 'Editar instituciones', 'module_key' => 'cat'],
            ['name' => 'instituciones.delete', 'description' => 'Eliminar instituciones', 'module_key' => 'cat'],

            // Priority Areas
            ['name' => 'priority_areas.index', 'description' => 'Ver áreas prioritarias', 'module_key' => 'cat'],
            ['name' => 'priority_areas.create', 'description' => 'Crear áreas prioritarias', 'module_key' => 'cat'],
            ['name' => 'priority_areas.edit', 'description' => 'Editar áreas prioritarias', 'module_key' => 'cat'],
            ['name' => 'priority_areas.delete', 'description' => 'Eliminar áreas prioritarias', 'module_key' => 'cat'],

            // Sub Areas
            ['name' => 'sub_areas.index', 'description' => 'Ver sub áreas', 'module_key' => 'cat'],
            ['name' => 'sub_areas.create', 'description' => 'Crear sub áreas', 'module_key' => 'cat'],
            ['name' => 'sub_areas.edit', 'description' => 'Editar sub áreas', 'module_key' => 'cat'],
            ['name' => 'sub_areas.delete', 'description' => 'Eliminar sub áreas', 'module_key' => 'cat'],
            // Rubrics
            ['name' => 'rubrics.index', 'description' => 'Ver rúbricas', 'module_key' => 'cat'],
            ['name' => 'rubrics.create', 'description' => 'Crear rúbricas', 'module_key' => 'cat'],
            ['name' => 'rubrics.edit', 'description' => 'Editar rúbricas', 'module_key' => 'cat'],
            ['name' => 'rubrics.delete', 'description' => 'Eliminar rúbricas', 'module_key' => 'cat'],

            // Calendario
            ['name' => 'calendario.index', 'description' => 'Ver calendario', 'module_key' => 'cat'],
            ['name' => 'calendario.create', 'description' => 'Crear calendario', 'module_key' => 'cat'],
            ['name' => 'calendario.edit', 'description' => 'Editar calendario', 'module_key' => 'cat'],
            ['name' => 'calendario.delete', 'description' => 'Eliminar calendario', 'module_key' => 'cat'],
          
            // Documents Module (Admin)
            ['name' => 'documents.index', 'description' => 'Ver documentos admin', 'module_key' => 'cat'],
            ['name' => 'documents.show', 'description' => 'Ver detalles de documentos', 'module_key' => 'cat'],
            ['name' => 'documents.download', 'description' => 'Descargar documentos admin', 'module_key' => 'cat'],

            // Control de Solicitudes (Resumen por Instituciones)
            ['name' => 'requests.index', 'description' => 'Ver control de solicitudes (resumen instituciones)', 'module_key' => 'controlsoli'],
            ['name' => 'requests.show', 'description' => 'Ver detalles de control de solicitudes', 'module_key' => 'controlsoli'],

            // Docente Specific
            ['name' => 'docente.inicio', 'description' => 'Ver inicio docente', 'module_key' => 'dashboard'],
            ['name' => 'docente.solicitudes.show', 'description' => 'Ver detalle de solicitud docente', 'module_key' => 'dashboard'],

            // Admin Dashboard
            ['name' => 'admin.inicio', 'description' => 'Ver dashboard de administrador', 'module_key' => 'dashboard'],
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

        // Admin - Permisos de gestión
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $adminPermissions = Permission::whereIn('name', [
                // Dashboard
                'admin.inicio',
                
                // Usuarios (Evaluadores Management)
                'users.index',
                'users.delete',
                
                // Solicitudes (Gestión Individual)
                'solicitudes.index',
                'solicitudes.create',
                'solicitudes.edit',
                'solicitudes.delete',
                'solicitudes.own',
                'solicitudes.assign',
                'solicitudes.verdict',
                
                // Evaluaciones
                'evaluaciones.index',
                'evaluaciones.create',
                'evaluaciones.edit',
                'evaluaciones.delete',
                
                // Reconocimientos
                'reconocimiento.index',
                'reconocimiento.create',
                'reconocimiento.edit',
                'reconocimiento.toggle',
            ])->get();
            $admin->syncPermissions($adminPermissions);
            $this->command->info("✅ {$adminPermissions->count()} permisos asignados al Admin.");
        }

        // Docente - Permisos de lectura y sus propias solicitudes
        $docente = Role::where('name', 'Docente')->first();
        if ($docente) {
            $docentePermissions = Permission::whereIn('name', [
                // Dashboard
                'docente.inicio',
                
                // Convocatorias (Vista Docente)
                'convocatorias.index',
                
                // Solicitudes (Solo propias)
                'solicitudes.own',
                'solicitudes.create',
                'solicitudes.edit',
                
                // Documentos
                'docente.solicitudes.show',
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