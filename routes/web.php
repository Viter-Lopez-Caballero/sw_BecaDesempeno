<?php

use Illuminate\Support\Facades\Route;
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
        $role = auth()->user()->getPrimaryRole();
        
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
        
        // Security Module - Solo Super Admin
        Route::resource('modules', ModuleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);
        
        // Control de Solicitudes
        Route::get('solicitudes', function () {
            return Inertia::render('Solicitudes/Index');
        })->name('solicitudes.index');

        // Convocatorias
        Route::get('convocatorias', function () {
            return Inertia::render('Convocatorias/Index');
        })->name('convocatorias.index');

        // Catálogo
        Route::get('catalogo/campus', function () {
            return Inertia::render('Catalogo/Campus');
        })->name('catalogo.campus');

        Route::get('catalogo/areas-prioritarias', function () {
            return Inertia::render('Catalogo/AreasPrioritarias');
        })->name('catalogo.areas');

        Route::get('catalogo/documentos', function () {
            return Inertia::render('Catalogo/Documentos');
        })->name('catalogo.documentos');

        Route::get('catalogo/calendario', function () {
            return Inertia::render('Catalogo/Calendario');
        })->name('catalogo.calendario');

        Route::get('catalogo/rubrica', function () {
            return Inertia::render('Catalogo/Rubrica');
        })->name('catalogo.rubrica');
    });

    // ========================
    // RUTAS ADMIN
    // ========================
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('inicio', [AdminController::class, 'inicio'])->name('inicio');
        
        // Usuarios - Solo Admin puede gestionar usuarios (no módulos/permisos/roles)
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        
        // Solicitudes
        Route::get('solicitudes', function () {
            return Inertia::render('Solicitudes/Index');
        })->name('solicitudes.index');

        // Convocatorias
        Route::get('convocatorias', function () {
            return Inertia::render('Convocatorias/Index');
        })->name('convocatorias.index');

        // Reconocimiento
        Route::get('reconocimiento', function () {
            return Inertia::render('Reconocimiento/Index');
        })->name('reconocimiento.index');

        // Catálogo
        Route::get('catalogo/campus', function () {
            return Inertia::render('Catalogo/Campus');
        })->name('catalogo.campus');

        Route::get('catalogo/areas-prioritarias', function () {
            return Inertia::render('Catalogo/AreasPrioritarias');
        })->name('catalogo.areas');

        Route::get('catalogo/documentos', function () {
            return Inertia::render('Catalogo/Documentos');
        })->name('catalogo.documentos');

        Route::get('catalogo/calendario', function () {
            return Inertia::render('Catalogo/Calendario');
        })->name('catalogo.calendario');

        Route::get('catalogo/rubrica', function () {
            return Inertia::render('Catalogo/Rubrica');
        })->name('catalogo.rubrica');
    });

    // ========================
    // RUTAS EVALUADOR
    // ========================
    Route::middleware(['role:Evaluador'])->prefix('evaluador')->name('evaluador.')->group(function () {
        Route::get('inicio', [EvaluadorController::class, 'inicio'])->name('inicio');
        
        // Evaluaciones
        Route::get('evaluaciones', function () {
            return Inertia::render('Evaluaciones/Index');
        })->name('evaluaciones.index');

        // Reconocimiento
        Route::get('reconocimiento', function () {
            return Inertia::render('Reconocimiento/Index');
        })->name('reconocimiento.index');
    });

    // ========================
    // RUTAS DOCENTE
    // ========================
    Route::middleware(['role:Docente'])->prefix('docente')->name('docente.')->group(function () {
        Route::get('inicio', [DocenteController::class, 'inicio'])->name('inicio');
        
        // Convocatorias - Solo ver
        Route::get('convocatorias', function () {
            return Inertia::render('Convocatorias/Index');
        })->name('convocatorias.index');
    });
});

require __DIR__.'/settings.php';

