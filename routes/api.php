<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurpController;

// API para buscar CURP (usado por Register.vue)
Route::post('/buscar-curp', [CurpController::class , 'search']);
