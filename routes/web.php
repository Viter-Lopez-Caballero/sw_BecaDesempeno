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

use App\Http\Controllers\ModuloController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PerfilesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('modulo', ModuloController::class)->names('modulo');
    Route::get('modulo/recover/{id}', [ModuloController::class, 'recover'])->name('modulo.recover');
    Route::resource('permissions', PermissionController::class)->names('permissions');
    
    Route::resource('usuarios', UsuarioController::class)->names('usuarios');
    Route::resource('perfiles', PerfilesController::class)->names('perfiles');
});

require __DIR__.'/settings.php';
