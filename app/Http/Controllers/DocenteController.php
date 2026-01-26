<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DocenteController extends Controller
{
    public function inicio()
    {
        return Inertia::render('Dashboard');
    }
}
