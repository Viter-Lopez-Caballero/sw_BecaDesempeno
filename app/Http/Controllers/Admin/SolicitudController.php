<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SolicitudResource;
use App\Models\Evaluacion;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SolicitudController extends Controller
{
    /**
     * Display a listing of Requests for Admin (with evaluator assignment).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $rows = $request->input('rows', 10);
        $status = $request->input('status'); // filter by status

        $query = Solicitud::with([
                'user.institucion', 
                'evaluaciones.evaluador',
                'convocatoria'
            ])
            ->when($search, function ($q, $search) {
                $q->whereHas('user', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            })
            ->when($status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->latest();

        $solicitudes = $query->paginate($rows)->withQueryString();

        // Get Evaluators for the Assignment Modal
        $evaluators = User::role('Evaluador')->select('id', 'name', 'email')->get();

        return Inertia::render('Admin/Solicitudes/Index', [
            'solicitudes' => SolicitudResource::collection($solicitudes),
            'evaluators' => $evaluators,
            'filters' => $request->all(['search', 'rows', 'status']),
        ]);
    }

    /**
     * Assign evaluators to a request.
     */
    public function assignEvaluator(Request $request)
    {
        $request->validate([
            'solicitud_id' => 'required|exists:solicitudes,id',
            'evaluator_ids' => 'required|array',
            'evaluator_ids.*' => 'exists:users,id',
        ]);

        $solicitudId = $request->solicitud_id;
        $evaluatorIds = $request->evaluator_ids;

        DB::transaction(function () use ($solicitudId, $evaluatorIds) {
            foreach ($evaluatorIds as $userId) {
                // Check if already assigned
                $exists = Evaluacion::where('solicitud_id', $solicitudId)
                                    ->where('user_id', $userId)
                                    ->exists();
                
                if (!$exists) {
                    Evaluacion::create([
                        'solicitud_id' => $solicitudId,
                        'user_id' => $userId,
                        'status' => 'pending',
                    ]);
                }
            }
        });

        return to_route('admin.solicitudes.index')->with('success', 'Evaluadores asignados correctamente.');
    }

    /**
     * Remove an evaluator from a request.
     */
    public function removeEvaluator(Request $request)
    {
        $request->validate([
            'solicitud_id' => 'required|exists:solicitudes,id',
            'evaluator_id' => 'required|exists:users,id',
        ]);

        Evaluacion::where('solicitud_id', $request->solicitud_id)
                  ->where('user_id', $request->evaluator_id)
                  ->delete();

        return back()->with('success', 'Evaluador removido.');
    }

    public function assignView($id)
    {
        $solicitud = Solicitud::with(['user.institucion', 'convocatoria', 'evaluaciones.evaluador'])
            ->findOrFail($id);

        $evaluators = User::role('Evaluador')->select('id', 'name', 'email')->get();

        return Inertia::render('Admin/Solicitudes/AssignEvaluator', [
            'solicitud' => (new SolicitudResource($solicitud))->resolve(),
            'evaluators' => $evaluators,
        ]);
    }

    /**
     * Display the specified request details + verdict form.
     */
    public function show($id)
    {
        $solicitud = Solicitud::with([
                'user.institucion', 
                'user.priorityArea',
                'user.subArea',
                'evaluaciones.evaluador', 
                'documentos', 
                'convocatoria'
            ])
            ->findOrFail($id);

        return Inertia::render('Admin/Solicitudes/Show', [
            'solicitud' => (new SolicitudResource($solicitud))->resolve(),
        ]);
    }

    /**
     * Submit Admin Verdict (Approve/Reject).
     */
    public function verdict(Request $request, $id)
    {
        $solicitud = Solicitud::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'comentario' => 'required_if:status,rejected|nullable|string|max:1000',
        ]);

        $solicitud->status = $validated['status'];
        if ($validated['status'] === 'rejected') {
            $solicitud->admin_comment = $validated['comentario'];
        }
        $solicitud->save();

        return to_route('admin.solicitudes.show', $id)->with('success', 'Veredicto registrado correctamente.');
    }
}
