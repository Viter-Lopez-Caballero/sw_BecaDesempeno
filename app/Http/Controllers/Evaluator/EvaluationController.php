<?php

namespace App\Http\Controllers\Evaluator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Evaluacion;

class EvaluationController extends Controller
{
    public function show($id)
    {
        // Placeholder for now
        // Eventually we will load the evaluacion with relations
        $evaluacion = Evaluacion::with('solicitud.convocatoria', 'solicitud.user', 'solicitud.documentos')->findOrFail($id);
        
        // Check authorization (policy can be added later, for now we assume middleware handles role, but user check is good)
        if ($evaluacion->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('Evaluator/Evaluations/Show', [
            'evaluation' => $evaluacion
        ]);
    }
}
