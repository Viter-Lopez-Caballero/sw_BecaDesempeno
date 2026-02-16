<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurpController;

// API para buscar CURP (usado por Register.vue)
Route::post('/buscar-curp', [CurpController::class, 'search']);

// API para obtener sub-áreas
Route::get('/sub-areas/{priority_area_id}', function ($priority_area_id) {
    return \App\Models\SubArea::where('priority_area_id', $priority_area_id)->get(['id', 'name']);
});
