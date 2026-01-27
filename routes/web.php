<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Inicio', [
        'canLogin' => Route::has('login'),
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('inicio');

Route::get('/convocatoria', function () {
    return Inertia::render('Convocatoria');
})->name('convocatoria');

Route::get('/documentos', function () {
    return Inertia::render('Documentos');
})->name('documentos');

Route::get('/contacto', function () {
    return Inertia::render('Contacto');
})->name('contacto');

use App\Http\Controllers\Security\ModuleController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\UserController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EvaluadorController;
use App\Http\Controllers\DocenteController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Ruta de inicio genérica (redirige según rol)
    Route::get('inicio', function () {
        $role = Auth::user()->getPrimaryRole();
        
        return match ($role) {
            'Super Admin' => redirect()->route('superadmin.inicio'),
            'Admin' => redirect()->route('admin.inicio'),
            'Evaluador' => redirect()->route('evaluador.inicio'),
            'Docente' => redirect()->route('docente.inicio'),
            default => Inertia::render('Dashboard'),
        };
    })->name('inicio.dashboard');

    // ========================
    // RUTAS SUPER ADMIN
    // ========================
    Route::middleware(['role:Super Admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('inicio', [SuperAdminController::class, 'inicio'])->name('inicio');
        
        // Security Module - Solo Super Admin con middleware de permisos
        Route::prefix('seguridad')->name('seguridad.')->group(function () {
            Route::resource('modules', ModuleController::class)
                ->middleware([
                    'permission:modules.index|modules.create|modules.edit|modules.delete'
                ]);
            
            Route::resource('permissions', PermissionController::class)
                ->middleware([
                    'permission:permissions.index|permissions.create|permissions.edit|permissions.delete'
                ]);
            
            Route::resource('roles', RoleController::class)
                ->middleware([
                    'permission:roles.index|roles.create|roles.edit|roles.delete'
                ]);
            
            Route::resource('users', UserController::class)
                ->middleware([
                    'permission:users.index|users.create|users.edit|users.delete'
                ]);
        });
        
        // Control de Solicitudes - SuperAdmin
        Route::get('solicitudes', function () {
            return Inertia::render('SuperAdmin/Solicitudes/Index', [
                'title' => 'Control de Solicitudes'
            ]);
        })->name('solicitudes.index');

        // Convocatorias - SuperAdmin
        Route::get('convocatorias', function () {
            return Inertia::render('SuperAdmin/Convocatorias/Index', [
                'title' => 'Gestión de Convocatorias'
            ]);
        })->name('convocatorias.index');

        // Catálogo - SuperAdmin
        Route::get('catalogo/campus', function () {
            return Inertia::render('SuperAdmin/Catalogo/Catalogo/Campus', [
                'title' => 'Catálogo de Campus'
            ]);
        })->name('catalogo.campus');

        Route::get('catalogo/areas-prioritarias', function () {
            return Inertia::render('SuperAdmin/Catalogo/Catalogo/AreasPrioritarias', [
                'title' => 'Áreas Prioritarias'
            ]);
        })->name('catalogo.areas');

        Route::get('catalogo/documentos', function () {
            return Inertia::render('SuperAdmin/Catalogo/Catalogo/Documentos', [
                'title' => 'Documentos'
            ]);
        })->name('catalogo.documentos');

        Route::get('catalogo/calendario', function () {
            return Inertia::render('SuperAdmin/Catalogo/Catalogo/Calendario', [
                'title' => 'Calendario'
            ]);
        })->name('catalogo.calendario');

        Route::get('catalogo/rubrica', function () {
            return Inertia::render('SuperAdmin/Catalogo/Catalogo/Rubrica', [
                'title' => 'Rúbrica de Evaluación'
            ]);
        })->name('catalogo.rubrica');
    });

    // ========================
    // RUTAS ADMIN
    // ========================
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('inicio', [AdminController::class, 'inicio'])->name('inicio');
        
        // Usuarios - Admin puede gestionar usuarios
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', function () {
                return Inertia::render('Admin/Usuarios/Index', [
                    'title' => 'Gestión de Usuarios'
                ]);
            })->name('index');
            
            Route::get('crear', function () {
                return Inertia::render('Admin/Usuarios/Create', [
                    'title' => 'Crear Usuario'
                ]);
            })->name('create');
            
            Route::get('{id}/editar', function ($id) {
                return Inertia::render('Admin/Usuarios/Edit', [
                    'title' => 'Editar Usuario',
                    'userId' => $id
                ]);
            })->name('edit');
        });
        
        // Solicitudes - Admin
        Route::get('solicitudes', function () {
            return Inertia::render('Admin/Solicitudes/Index', [
                'title' => 'Control de Solicitudes'
            ]);
        })->name('solicitudes.index');

        // Reconocimiento - Admin
        Route::get('reconocimiento', function () {
            return Inertia::render('Admin/Reconocimiento/Index', [
                'title' => 'Reconocimientos'
            ]);
        })->name('reconocimiento.index');
    });

    // ========================
    // RUTAS EVALUADOR
    // ========================
    Route::middleware(['role:Evaluador'])->prefix('evaluador')->name('evaluador.')->group(function () {
        Route::get('inicio', [EvaluadorController::class, 'inicio'])->name('inicio');
        
        // Evaluaciones - Evaluador
        Route::get('evaluaciones', function () {
            return Inertia::render('Evaluador/Evaluaciones/Index', [
                'title' => 'Mis Evaluaciones'
            ]);
        })->name('evaluaciones.index');

        // Reconocimiento - Evaluador
        Route::get('reconocimiento', function () {
            return Inertia::render('Evaluador/Reconocimiento/Index', [
                'title' => 'Reconocimientos'
            ]);
        })->name('reconocimiento.index');
    });

    // ========================
    // RUTAS DOCENTE
    // ========================
    Route::middleware(['role:Docente'])->prefix('docente')->name('docente.')->group(function () {
        Route::get('inicio', [DocenteController::class, 'inicio'])->name('inicio');
        
        // Convocatorias - Docente (solo ver)
        Route::get('convocatorias', function () {
            return Inertia::render('Docente/Convocatorias/Index', [
                'title' => 'Convocatorias Disponibles'
            ]);
        })->name('convocatorias.index');
    });
});

require __DIR__.'/settings.php';
