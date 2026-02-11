<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    // Si el usuario está autenticado, redirigir a su dashboard
    if (auth()->check()) {
        $role = auth()->user()->getPrimaryRole();
        
        return match ($role) {
            'Super Admin' => redirect()->route('superadmin.inicio'),
            'Admin' => redirect()->route('admin.inicio'),
            'Evaluador' => redirect()->route('evaluador.inicio'),
            'Docente' => redirect()->route('docente.inicio'),
            default => redirect()->route('inicio.dashboard'),
        };
    }
    
    // Si no está autenticado, mostrar la página pública
    $convocatorias = \App\Models\Convocatoria::query()
        ->with('calendario')
        ->whereIn('estado', ['activa', 'cerrada'])
        ->latest('created_at')
        ->paginate(3)
        ->withQueryString();
    
    // Obtener convocatoria para la línea de tiempo (Prioridad: Activa > Pendiente > Cerrada)
    $timelineConvocatoria = \App\Models\Convocatoria::query()
        ->with('calendario')
        ->where('estado', 'activa')
        ->latest('created_at')
        ->first();

    if (!$timelineConvocatoria) {
        $timelineConvocatoria = \App\Models\Convocatoria::query()
            ->with('calendario')
            ->where('estado', 'pendiente')
            ->latest('created_at')
            ->first();
    }

    if (!$timelineConvocatoria) {
        $timelineConvocatoria = \App\Models\Convocatoria::query()
            ->with('calendario')
            ->where('estado', 'cerrada')
            ->latest('created_at')
            ->first();
    }
    
    return Inertia::render('Inicio', [
        'canLogin' => Route::has('login'),
        'canRegister' => Features::enabled(Features::registration()),
        'convocatorias' => \App\Http\Resources\Catalogos\ConvocatoriaResource::collection($convocatorias),
        'timelineConvocatoria' => $timelineConvocatoria ? new \App\Http\Resources\Catalogos\ConvocatoriaResource($timelineConvocatoria) : null,
    ]);
})->name('inicio');

// (visits endpoint removed)

Route::get('/convocatoria', function () {
    // Prioridad: Activa > Pendiente > Cerrada
    $convocatoria = \App\Models\Convocatoria::with('calendario')
        ->where('estado', 'activa')
        ->latest('created_at')
        ->first();

    if (!$convocatoria) {
        $convocatoria = \App\Models\Convocatoria::with('calendario')
            ->where('estado', 'pendiente')
            ->latest('created_at')
            ->first();
    }

    if (!$convocatoria) {
        $convocatoria = \App\Models\Convocatoria::with('calendario')
            ->where('estado', 'cerrada')
            ->latest('created_at')
            ->first();
    }
    
    return Inertia::render('Convocatoria', [
        'convocatoria' => $convocatoria ? new \App\Http\Resources\Catalogos\ConvocatoriaResource($convocatoria) : null,
    ]);
})->name('convocatoria');

Route::get('/documentos', function () {
    return Inertia::render('Documentos');
})->name('documentos');

Route::get('/contacto', function () {
    return Inertia::render('Contacto');
})->name('contacto');

// API para obtener instituciones (contacto)
Route::get('/api/instituciones', [App\Http\Controllers\ContactController::class, 'getInstituciones'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// API para enviar formulario de contacto
Route::post('/api/contacto', [App\Http\Controllers\ContactController::class, 'sendContact'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CurpController;

// API para buscar CURP (usado por Register.vue) - Sin CSRF
Route::post('/api/buscar-curp', [CurpController::class, 'buscar'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// API para obtener sub-áreas - Sin CSRF
Route::get('/api/sub-areas/{priority_area_id}', function ($priority_area_id) {
    return \App\Models\SubArea::where('priority_area_id', $priority_area_id)->get(['id', 'name']);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Rutas de registro personalizadas (sobrescriben Fortify)
Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::get('/register/evaluador', [RegisterController::class, 'createEvaluador'])->name('register.evaluador');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

// Rutas de verificación de email
Route::get('/email/verify', function () {
    // Si el usuario está autenticado y ya verificó su email, redirigir a su dashboard
    if (auth()->check() && auth()->user()->hasVerifiedEmail()) {
        $role = auth()->user()->getPrimaryRole();
        
        return match ($role) {
            'Super Admin' => redirect()->route('superadmin.inicio'),
            'Admin' => redirect()->route('admin.inicio'),
            'Evaluador' => redirect()->route('evaluador.inicio'),
            'Docente' => redirect()->route('docente.inicio'),
            default => redirect()->route('inicio'),
        };
    }
    
    return \Inertia\Inertia::render('Auth/VerifyEmail', [
        'email' => session('email'),
        'status' => session('status'),
    ]);
})->name('verification.notice');

Route::post('/email/verify/code', [CurpController::class, 'verificarCodigo'])->name('verification.verify');
Route::post('/email/verify/resend', [CurpController::class, 'reenviarCodigo'])->name('verification.resend');

use App\Http\Controllers\Security\ModuleController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\UserController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\RequestControlController;
use App\Http\Controllers\Admin\SolicitudController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EvaluadorController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\Catalogos\InstitutionController;
use App\Http\Controllers\Catalogos\PriorityAreaController;
use App\Http\Controllers\Catalogos\SubAreaController;
use App\Http\Controllers\Catalogos\RubricController;
use App\Http\Controllers\Catalogos\CalendarioController;
use App\Http\Controllers\Catalogos\ConvocatoriaController;
use App\Http\Controllers\Catalogos\DocumentoController;

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
        Route::get('inicio', [\App\Http\Controllers\SuperAdmin\InicioController::class, 'inicio'])->name('inicio');
        Route::get('control-solicitudes', [\App\Http\Controllers\SuperAdmin\RequestControlController::class, 'index'])->name('control-solicitudes');
    });

    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('inicio', [AdminController::class, 'inicio'])->name('inicio');
        
        // Evaluadores Management
        Route::get('evaluadores', [\App\Http\Controllers\Admin\EvaluadorController::class, 'index'])->name('evaluadores.index');
        Route::delete('evaluadores/{id}', [\App\Http\Controllers\Admin\EvaluadorController::class, 'destroy'])->name('evaluadores.destroy');
        
        // Reconocimientos
        Route::get('reconocimientos', [\App\Http\Controllers\Admin\ReconocimientoController::class, 'index'])->name('reconocimientos.index');
        Route::post('reconocimientos/toggle', [\App\Http\Controllers\Admin\ReconocimientoController::class, 'toggle'])->name('reconocimientos.toggle');
    });

    Route::middleware(['role:Evaluador'])->prefix('evaluador')->name('evaluador.')->group(function () {
        Route::get('inicio', [\App\Http\Controllers\Evaluador\EvaluadorController::class, 'inicio'])->name('inicio');
        Route::get('evaluacion/{id}', [\App\Http\Controllers\Evaluador\EvaluadorController::class, 'show'])->name('evaluacion.show');
        Route::put('evaluacion/{id}', [\App\Http\Controllers\Evaluador\EvaluadorController::class, 'evaluar'])->name('evaluacion.update');
        Route::get('documentos/{id}/stream', [\App\Http\Controllers\Evaluador\EvaluadorController::class, 'streamDocument'])->name('documentos.stream');

        // Historial de Evaluaciones
        Route::get('evaluaciones', [\App\Http\Controllers\Evaluador\EvaluadorController::class, 'index'])->name('evaluaciones.index');
        Route::get('evaluaciones/{id}', [\App\Http\Controllers\Evaluador\EvaluadorController::class, 'showHistory'])->name('evaluaciones.show');

        // Reconocimientos
        Route::get('reconocimientos', [\App\Http\Controllers\Evaluador\ReconocimientoController::class, 'index'])->name('reconocimientos.index');
        Route::get('reconocimientos/{id}/download', [\App\Http\Controllers\Evaluador\ReconocimientoController::class, 'download'])->name('reconocimientos.download');
    });

    Route::middleware(['role:Docente'])->prefix('docente')->name('docente.')->group(function () {
        Route::get('inicio', [DocenteController::class, 'inicio'])->name('inicio');
        Route::get('solicitudes/{id}', [DocenteController::class, 'show'])->name('solicitudes.show');
        Route::get('solicitudes/download/{id}', [DocenteController::class, 'download'])->name('solicitudes.download');
        Route::get('solicitudes/stream/{id}', [DocenteController::class, 'stream'])->name('solicitudes.stream');
        
        // Convocatorias Docente
        Route::get('convocatorias', [DocenteController::class, 'convocatorias'])->name('convocatorias.index');
        Route::get('convocatorias/{id}/solicitar', [DocenteController::class, 'solicitar'])->name('convocatorias.solicitar');
        Route::post('convocatorias/solicitar', [DocenteController::class, 'storeSolicitud'])->name('solicitudes.store');
    });

    // ========================
    // MÓDULO DE SEGURIDAD
    // ========================
    // Protegido por permisos específicos. 
    // Nota: Los controladores ya deberían verificar permisos independientemente.
    
    Route::prefix('seguridad')->name('seguridad.')->group(function () {
        Route::resource('modules', ModuleController::class);
        // ->middleware('can:modules.index'); // Opcional si el controlador ya gestiona

     // Documents Module Routes
    Route::get('documentos', [DocumentController::class, 'index'])->name('documents.index')->middleware('can:documents.index');
    Route::get('documentos/{id}', [DocumentController::class, 'show'])->name('documents.show')->middleware('can:documents.show');
    Route::get('documentos/download/{documento}', [DocumentController::class, 'download'])->name('documents.download')->middleware('can:documents.download');

        Route::resource('permissions', PermissionController::class);
        // ->middleware('can:permissions.index');

        Route::resource('roles', RoleController::class);
        // ->middleware('can:roles.index');

        Route::get('users/export', [UserController::class, 'export'])->name('users.export'); // ->middleware('can:users.index');
        Route::get('users/template', [UserController::class, 'template'])->name('users.template'); // ->middleware('can:users.index');
        Route::post('users/import', [UserController::class, 'import'])->name('users.import'); // ->middleware('can:users.create');
        Route::resource('users', UserController::class);
        // ->middleware('can:users.index');
    });


    // ========================
    // MÓDULOS COMPARTIDOS
    // ========================
    
    Route::get('control-solicitudes', [RequestControlController::class, 'index'])->name('solicitudes.index')->middleware('can:requests.index');
    Route::get('control-solicitudes/{id}', [RequestControlController::class, 'show'])->name('solicitudes.show')->middleware('can:requests.show');

    // Admin Specific Solicitudes Logic
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('inicio', [AdminController::class, 'inicio'])->name('inicio');
        // Admin Solicitudes (List, Assign, Verdict)
        Route::get('solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
        Route::get('solicitudes/{id}/assign', [SolicitudController::class, 'assignView'])->name('solicitudes.assign_view');
        Route::post('solicitudes/assign', [SolicitudController::class, 'assignEvaluator'])->name('solicitudes.assign');
        Route::delete('solicitudes/evaluador', [SolicitudController::class, 'removeEvaluator'])->name('solicitudes.remove-evaluator');
        Route::get('solicitudes/{id}', [SolicitudController::class, 'show'])->name('solicitudes.show');
        Route::post('solicitudes/{id}/verdict', [SolicitudController::class, 'verdict'])->name('solicitudes.verdict');
        // Documents (Existing?)
        // ...
    });

    // Convocatorias
    Route::get('convocatorias/{convocatoria}/download', [ConvocatoriaController::class, 'download'])->name('convocatorias.download');
    Route::put('convocatorias/{convocatoria}/documentos', [ConvocatoriaController::class, 'updateDocumentos'])->name('convocatorias.updateDocumentos');
    Route::resource('convocatorias', ConvocatoriaController::class)->names([
        'index' => 'convocatorias.index',
        'create' => 'convocatorias.create',
        'store' => 'convocatorias.store',
        'edit' => 'convocatorias.edit',
        'update' => 'convocatorias.update',
        'destroy' => 'convocatorias.destroy',
    ]);

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

        Route::get('documentos/{id}/download', [DocumentoController::class, 'download'])->name('documentos.download');
        Route::post('documentos/{id}/toggle-active', [DocumentoController::class, 'toggleActive'])->name('documentos.toggleActive');
        Route::get('documentos/{documento}/download-docente', [DocumentoController::class, 'downloadDocente'])->name('documentos.downloadDocente');
        Route::get('documentos/{documento}/stream-docente', [DocumentoController::class, 'streamDocente'])->name('documentos.streamDocente');
        Route::resource('documentos', DocumentoController::class);

        // Documentos de Docentes - REMOVED (integrated into documentos with tabs)
        // Mantener estas rutas comentadas para referencia si se necesitan en el futuro

        Route::get('rubrica', function () {
            return Inertia::render('Catalogo/Rubrica');
        })->name('rubrica')->middleware('can:catalogo.index');

        Route::get('institutions/export', [InstitutionController::class, 'export'])->name('institutions.export'); // ->middleware('can:catalogo.index');
        Route::post('institutions/import', [InstitutionController::class, 'import'])->name('institutions.import'); // ->middleware('can:catalogo.index');
        Route::get('institutions/template', [InstitutionController::class, 'downloadTemplate'])->name('institutions.template'); // ->middleware('can:catalogo.index');
        Route::resource('institutions', InstitutionController::class);

        Route::get('priority-areas/export', [PriorityAreaController::class, 'export'])->name('priority-areas.export'); // ->middleware('can:catalogo.index');
        Route::post('priority-areas/import', [PriorityAreaController::class, 'import'])->name('priority-areas.import'); // ->middleware('can:catalogo.index');
        Route::get('priority-areas/template', [PriorityAreaController::class, 'downloadTemplate'])->name('priority-areas.template'); // ->middleware('can:catalogo.index');
        Route::resource('priority-areas', PriorityAreaController::class);
        
        Route::get('sub-areas/export', [SubAreaController::class, 'export'])->name('sub-areas.export'); // ->middleware('can:catalogo.index');
        // No import for sub-areas as requested
        Route::resource('sub-areas', SubAreaController::class);

        Route::resource('rubrics', RubricController::class);
        Route::post('rubrics/{rubric}/toggle-active', [RubricController::class, 'toggleActive'])->name('rubrics.toggle-active');
        Route::resource('calendario', CalendarioController::class);
    });

    // Modules accessible by permission (Admin/SuperAdmin)
    Route::middleware(['can:documents.index'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('documents', \App\Http\Controllers\Admin\DocumentController::class)->only(['index', 'show']);
        Route::get('documents/{documento}/download', [\App\Http\Controllers\Admin\DocumentController::class, 'download'])->name('documents.download');
        Route::get('documents/{documento}/stream', [\App\Http\Controllers\Admin\DocumentController::class, 'stream'])->name('documents.stream');
    });
});

require __DIR__.'/settings.php';
