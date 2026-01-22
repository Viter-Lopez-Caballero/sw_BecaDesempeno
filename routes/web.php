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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Security Module
    Route::resource('modules', ModuleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
});

require __DIR__.'/settings.php';

