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
use App\Http\Controllers\Catalogos\InstitutionController;

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
    // RUTAS SUPER ADMIN DASHBOARD
    // ========================
    // Mantenemos el dashboard principal exclusivo por Rol si se desea, 
    // o podríamos protegerlo con un permiso especial. Por ahora lo dejamos por rol para no romper el inicio.
    Route::middleware(['role:Super Admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('inicio', [SuperAdminController::class, 'inicio'])->name('inicio');
    });

    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('inicio', [AdminController::class, 'inicio'])->name('inicio');
    });

    Route::middleware(['role:Evaluador'])->prefix('evaluador')->name('evaluador.')->group(function () {
        Route::get('inicio', [EvaluadorController::class, 'inicio'])->name('inicio');
    });

    Route::middleware(['role:Docente'])->prefix('docente')->name('docente.')->group(function () {
        Route::get('inicio', [DocenteController::class, 'inicio'])->name('inicio');
    });

    // ========================
    // MÓDULO DE SEGURIDAD
    // ========================
    // Protegido por permisos específicos. 
    // Nota: Los controladores ya deberían verificar permisos independientemente.
    
    Route::prefix('seguridad')->name('seguridad.')->group(function () {
        Route::resource('modules', ModuleController::class);
        // ->middleware('can:modules.index'); // Opcional si el controlador ya gestiona

        Route::resource('permissions', PermissionController::class);
        // ->middleware('can:permissions.index');

        Route::resource('roles', RoleController::class);
        // ->middleware('can:roles.index');

        Route::resource('users', UserController::class);
        // ->middleware('can:users.index');
    });


    // ========================
    // MÓDULOS COMPARTIDOS
    // ========================
    
    // Control de Solicitudes
    Route::get('solicitudes', function () {
        return Inertia::render('Solicitudes/Index');
    })->name('solicitudes.index')->middleware('can:solicitudes.index');

    // Convocatorias
    Route::get('convocatorias', function () {
        return Inertia::render('Convocatorias/Index');
    })->name('convocatorias.index')->middleware('can:convocatorias.index');

    // Reconocimiento
    Route::get('reconocimiento', function () {
        return Inertia::render('Reconocimiento/Index');
    })->name('reconocimiento.index')->middleware('can:reconocimiento.index');

    // Evaluaciones
    Route::get('evaluaciones', function () {
        return Inertia::render('Evaluaciones/Index');
    })->name('evaluaciones.index')->middleware('can:evaluaciones.index');

    // Catálogo
    Route::prefix('catalogo')->name('catalogo.')->group(function() {
        Route::get('campus', function () {
            return Inertia::render('Catalogo/Campus');
        })->name('campus')->middleware('can:catalogo.index');

        Route::get('areas-prioritarias', function () {
            return Inertia::render('Catalogo/AreasPrioritarias');
        })->name('areas')->middleware('can:catalogo.index');

        Route::get('documentos', function () {
            return Inertia::render('Catalogo/Documentos');
        })->name('documentos')->middleware('can:catalogo.index');

        Route::get('calendario', function () {
            return Inertia::render('Catalogo/Calendario');
        })->name('calendario')->middleware('can:catalogo.index');

        Route::get('rubrica', function () {
            return Inertia::render('Catalogo/Rubrica');
        })->name('rubrica')->middleware('can:catalogo.index');

        Route::resource('institutions', InstitutionController::class);
    });
});

require __DIR__.'/settings.php';
