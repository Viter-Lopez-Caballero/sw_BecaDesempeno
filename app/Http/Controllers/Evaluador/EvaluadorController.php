<?php

namespace App\Http\Controllers\Evaluador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Evaluacion;
use App\Models\Solicitud;

class EvaluadorController extends Controller
{
    public function inicio(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        // Estadísticas
        $totalAsignadas = Evaluacion::where('user_id', $user->id)->count();
        $pendientes = Evaluacion::where('user_id', $user->id)->where('status', 'pending')->count();
        $evaluadas = $totalAsignadas - $pendientes;

        // Solicitudes Pendientes
        $solicitudes = Evaluacion::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with([
                'solicitud.convocatoria',
                'solicitud.user.subArea', 
                'solicitud' => function ($query) {
                    $query->withCount('documentos');
                }
            ])
            ->whereHas('solicitud', function ($query) use ($search) {
                if ($search) {
                     $query->whereHas('convocatoria', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%");
                     })
                     ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                     });
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('rows', 10))
            ->withQueryString();

        // Transformar datos para la vista
        $solicitudes->getCollection()->transform(function ($evaluacion) {
            return [
                'id' => $evaluacion->solicitud->id,
                'evaluacion_id' => $evaluacion->id,
                'convocatoria_nombre' => $evaluacion->solicitud->convocatoria->nombre,
                'docente_nombre' => $evaluacion->solicitud->user->name,
                // Usamos SubArea como proxy de "Grado/Especialidad" si no existe campo específico
                'docente_grado' => $evaluacion->solicitud->user->subArea?->name ?? 'N/A',
                'fecha_solicitud' => $evaluacion->solicitud->created_at->isoFormat('D [de] MMMM [de] YYYY'),
                'documentos_count' => $evaluacion->solicitud->documentos_count,
                'status' => 'Pendiente', // Hardcoded porque filtramos por pending
            ];
        });

        return Inertia::render('Evaluador/Dashboard/Index', [
            'stats' => [
                'total' => $totalAsignadas,
                'pendientes' => $pendientes,
                'evaluadas' => $evaluadas,
            ],
            'filters' => $request->all(['search', 'rows']),
            'solicitudes' => $solicitudes,
        ]);
    }
    public function show($id)
    {
        $evaluacion = Evaluacion::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->with([
                'solicitud.convocatoria',
                'solicitud.user.subArea',
                'solicitud.user.priorityArea',
                'solicitud.user.institucion',
                'solicitud.documentos'
            ])
            ->firstOrFail();

        // Get Active Rubric (assuming there is one active)
        $rubric = \App\Models\Rubric::where('is_active', true)
            ->with(['questions.options'])
            ->first();

        // Ensure all relationships are loaded
        // We keep the load to ensure 'institucion' and others are present
        $solicitud = $evaluacion->solicitud;
        $solicitud->load(['convocatoria', 'documentos', 'user.institucion', 'user.priorityArea', 'user.subArea']);

        return Inertia::render('Evaluador/Evaluacion/Show', [
            'evaluacion' => $evaluacion,
            'solicitud' => $solicitud,
            'rubric' => $rubric,
            'docente' => $solicitud->user,
        ]);
    }

    public function evaluar(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'score' => 'required|numeric|min:0',
            'respuestas' => 'required|array',
            'comentario' => 'required_if:status,rejected|nullable|string|max:1000',
        ]);

        $evaluacion = Evaluacion::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $evaluacion->update([
            'status' => $request->status,
            'score' => $request->score,
            'respuestas' => $request->respuestas,
            'comentario' => $request->comentario,
        ]);

        return redirect()->route('evaluador.inicio')->with('success', 'Evaluación registrada correctamente.');
    }

    public function streamDocument($id)
    {
        $document = \App\Models\Documento::findOrFail($id);
        
        // Authorization: Check if the evaluator is assigned to the request this document belongs to
        $hasAccess = Evaluacion::where('user_id', Auth::id())
            ->where('solicitud_id', $document->solicitud_id)
            ->exists();
            
        if (!$hasAccess) {
            abort(403, 'No tienes permiso para ver este documento.');
        }

        $path = $document->file_path;
        
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            abort(404, 'El archivo no existe.');
        }

        // Return the file for streaming (inline view)
        return \Illuminate\Support\Facades\Storage::disk('public')->response($path, $document->name, [
            'Content-Disposition' => 'inline; filename="' . $document->name . '"'
        ]);
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        // Estadísticas Detalladas
        $totalAsignadas = Evaluacion::where('user_id', $user->id)->count();
        $pendientes = Evaluacion::where('user_id', $user->id)->where('status', 'pending')->count();
        $evaluadas = $totalAsignadas - $pendientes;
        
        $aprobadas = Evaluacion::where('user_id', $user->id)->where('status', 'approved')->count();
        $rechazadas = Evaluacion::where('user_id', $user->id)->where('status', 'rejected')->count();

        // Solicitudes Completadas (History)
        $solicitudes = Evaluacion::where('user_id', $user->id)
            ->where('status', '!=', 'pending')
            ->with([
                'solicitud.convocatoria',
                'solicitud.user.subArea', 
                'solicitud' => function ($query) {
                    $query->withCount('documentos');
                }
            ])
            ->whereHas('solicitud', function ($query) use ($search) {
                if ($search) {
                     $query->whereHas('convocatoria', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%");
                     })
                     ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                     });
                }
            })
            ->orderBy('updated_at', 'desc') // Ordenar por fecha de evaluación
            ->paginate($request->input('rows', 10))
            ->withQueryString();

        // Transformar datos para la vista
        $solicitudes->getCollection()->transform(function ($evaluacion) {
            return [
                'id' => $evaluacion->solicitud->id,
                'evaluacion_id' => $evaluacion->id,
                'convocatoria_nombre' => $evaluacion->solicitud->convocatoria->nombre,
                'docente_nombre' => $evaluacion->solicitud->user->name,
                'docente_grado' => $evaluacion->solicitud->user->subArea?->name ?? 'N/A',
                'fecha_solicitud' => $evaluacion->solicitud->created_at->isoFormat('D [de] MMMM [de] YYYY'),
                'fecha_evaluacion' => $evaluacion->updated_at->isoFormat('D [de] MMMM [de] YYYY'),
                'documentos_count' => $evaluacion->solicitud->documentos_count,
                'status' => $evaluacion->status,
                'score' => $evaluacion->score,
            ];
        });

        return Inertia::render('Evaluador/Evaluaciones/Index', [
            'stats' => [
                'total' => $totalAsignadas,
                'pendientes' => $pendientes,
                'evaluadas' => $evaluadas,
                'aprobadas' => $aprobadas,
                'rechazadas' => $rechazadas,
            ],
            'filters' => $request->all(['search', 'rows']),
            'solicitudes' => $solicitudes,
        ]);
    }

    public function showHistory($id)
    {
        $evaluacion = Evaluacion::where('id', $id)
            ->where('user_id', Auth::id())
            // Remove 'pending' check to allow viewing history
            ->with([
                'solicitud.convocatoria',
                'solicitud.user.subArea',
                'solicitud.user.priorityArea',
                'solicitud.user.institucion',
                'solicitud.documentos'
            ])
            ->firstOrFail();

        // Get the rubric used (ideally this should be snapshotted, but for now we use active or just structure)
        // If we store rubric answers, we can reconstruct it. 
        // We fetch the active rubric to show structure. 
        // NOTE: If rubric changes, history viewing might be slightly off structure-wise if not snapshotted.
        // Assuming rubric doesn't change often or we only care about answers.
        $rubric = \App\Models\Rubric::where('is_active', true)
            ->with(['questions.options'])
            ->first();

        // Ensure all relationships are loaded
        $solicitud = $evaluacion->solicitud;
        $solicitud->load(['convocatoria', 'documentos', 'user.institucion', 'user.priorityArea', 'user.subArea']);

        return Inertia::render('Evaluador/Evaluaciones/Show', [
            'evaluacion' => $evaluacion,
            'solicitud' => $solicitud,
            'rubric' => $rubric,
            'docente' => $solicitud->user,
        ]);
    }
}
