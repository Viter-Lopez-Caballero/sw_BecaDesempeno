<?php

namespace App\Http\Controllers\Evaluator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Evaluacion;
use App\Models\Application;

class EvaluatorController extends Controller
{
    public function inicio(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        // Statistics
        $totalAsignadas = \App\Models\Evaluation::where('user_id', $user->id)->count();
        $pendientes = \App\Models\Evaluation::where('user_id', $user->id)->where('status', 'pending')->count();
        $evaluadas = $totalAsignadas - $pendientes;

        // Pending Applications
        $applications = \App\Models\Evaluation::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with([
                'application.announcement',
                'application.user.subArea', 
                'application' => function ($query) {
                    $query->withCount('documents');
                }
            ])
            ->whereHas('application', function ($query) use ($search) {
                if ($search) {
                     $query->whereHas('announcement', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                     })
                     ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                     });
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('rows', 10))
            ->withQueryString();

        // Transform data for view
        $applications->getCollection()->transform(function ($evaluation) {
            return [
                'id' => $evaluation->application->id,
                'evaluation_id' => $evaluation->id,
                'announcement_name' => $evaluation->application->announcement->name,
                'teacher_name' => $evaluation->application->user->name,
                'teacher_degree' => $evaluation->application->user->subArea?->name ?? 'N/A',
                'application_date' => $evaluation->application->created_at->isoFormat('D [de] MMMM [de] YYYY'),
                'documents_count' => $evaluation->application->documents_count ?? 0,
                'status' => 'Pendiente', 
            ];
        });

        return Inertia::render('Evaluator/Dashboard', [
            'stats' => [
                'total' => $totalAsignadas,
                'pendientes' => $pendientes,
                'evaluadas' => $evaluadas,
            ],
            'filters' => $request->all(['search', 'rows']),
            'applications' => $applications,
        ]);
    }

    public function show($id)
    {
        $evaluation = \App\Models\Evaluation::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->with([
                'application.announcement',
                'application.user.subArea',
                'application.user.priorityArea',
                'application.user.institution',
                'application.documents'
            ])
            ->firstOrFail();

        // Get Active Rubric
        $rubric = \App\Models\Rubric::where('is_active', true)
            ->with(['questions.options'])
            ->first();

        // Ensure all relationships are loaded
        $application = $evaluation->application;
        $application->load(['announcement', 'documents', 'user.institucion', 'user.priorityArea', 'user.subArea']);

        return Inertia::render('Evaluator/Evaluation/Show', [
            'evaluation' => $evaluation,
            'application' => $application,
            'rubric' => $rubric,
            'teacher' => $application->user,
        ]);
    }

    public function evaluar(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'score' => 'required|numeric|min:0',
            'answers' => 'required|array',
            'comment' => 'required_if:status,rejected|nullable|string|max:1000',
        ]);

        $evaluation = \App\Models\Evaluation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $evaluation->update([
            'status' => $request->status,
            'score' => $request->score,
            'answers' => $request->answers,
            'comment' => $request->comment,
        ]);

        return redirect()->route('evaluator.dashboard')->with('success', 'Evaluación registrada correctamente.');
    }

    public function streamDocument($id)
    {
        $document = \App\Models\Document::findOrFail($id);
        
        // Authorization: Check if the evaluator is assigned to the application this document belongs to
        $hasAccess = \App\Models\Evaluation::where('user_id', Auth::id())
            ->where('application_id', $document->application_id)
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

        // Statistics
        $totalAsignadas = \App\Models\Evaluation::where('user_id', $user->id)->count();
        $pendientes = \App\Models\Evaluation::where('user_id', $user->id)->where('status', 'pending')->count();
        $evaluadas = $totalAsignadas - $pendientes;
        
        $aprobadas = \App\Models\Evaluation::where('user_id', $user->id)->where('status', 'approved')->count();
        $rechazadas = \App\Models\Evaluation::where('user_id', $user->id)->where('status', 'rejected')->count();

        // Completed Applications (History)
        $applications = \App\Models\Evaluation::where('user_id', $user->id)
            ->where('status', '!=', 'pending')
            ->with([
                'application.announcement',
                'application.user.subArea', 
                'application' => function ($query) {
                    $query->withCount('documents');
                }
            ])
            ->whereHas('application', function ($query) use ($search) {
                if ($search) {
                     $query->whereHas('announcement', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                     })
                     ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                     });
                }
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($request->input('rows', 10))
            ->withQueryString();

        // Transform data
        $applications->getCollection()->transform(function ($evaluation) {
            return [
                'id' => $evaluation->application->id,
                'evaluation_id' => $evaluation->id,
                'announcement_name' => $evaluation->application->announcement->name,
                'teacher_name' => $evaluation->application->user->name,
                'teacher_degree' => $evaluation->application->user->subArea?->name ?? 'N/A',
                'application_date' => $evaluation->application->created_at->isoFormat('D [de] MMMM [de] YYYY'),
                'evaluation_date' => $evaluation->updated_at->isoFormat('D [de] MMMM [de] YYYY'),
                'documents_count' => $evaluation->application->documents_count ?? 0,
                'status' => $evaluation->status,
                'score' => $evaluation->score,
            ];
        });

        return Inertia::render('Evaluator/Evaluations/Index', [
            'stats' => [
                'total' => $totalAsignadas,
                'pendientes' => $pendientes,
                'evaluadas' => $evaluadas,
                'aprobadas' => $aprobadas,
                'rechazadas' => $rechazadas,
            ],
            'filters' => $request->all(['search', 'rows']),
            'applications' => $applications,
        ]);
    }

    public function showHistory($id)
    {
        $evaluation = \App\Models\Evaluation::where('id', $id)
            ->where('user_id', Auth::id())
            ->with([
                'application.announcement',
                'application.user.subArea',
                'application.user.priorityArea',
                'application.user.institucion',
                'application.documents'
            ])
            ->firstOrFail();

        $rubric = \App\Models\Rubric::where('is_active', true)
            ->with(['questions.options'])
            ->first();

        $application = $evaluation->application;
        $application->load(['announcement', 'documents', 'user.institucion', 'user.priorityArea', 'user.subArea']);

        return Inertia::render('Evaluator/Evaluations/Show', [
            'evaluation' => $evaluation,
            'application' => $application,
            'rubric' => $rubric,
            'teacher' => $application->user,
        ]);
    }
}
