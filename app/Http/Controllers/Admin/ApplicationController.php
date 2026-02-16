<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\Filterable;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    use Filterable;
    /**
     * Display a listing of Requests for Admin (with evaluator assignment).
     */
    public function index(Request $request)
    {
        $filters = $this->getFiltersBase($request->query());
        $status = $request->input('status');
        
        // Merge status into filters object for view
        $filters->status = $status;

        $query = \App\Models\Application::with([
                'user.institution', 
                'evaluations.evaluator', // Relationship rename? Check Application model
                'announcement'
            ])
            ->buscarGlobal($filters->search)
            ->porEstatus($status)
            ->ordenado($filters->order, $filters->direction);

        $applications = $query->paginate($filters->rows)->withQueryString();

        // Get Evaluators for the Assignment Modal
        $evaluators = User::role('Evaluador')->select('id', 'name', 'email')->get();

        return Inertia::render('Admin/Applications/Index', [
            'applications' => ApplicationResource::collection($applications),
            'evaluators' => $evaluators,
            'filters' => $filters,
        ]);
    }

    /**
     * Assign evaluators to a request.
     */
    public function assignEvaluator(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'evaluator_ids' => 'required|array',
            'evaluator_ids.*' => 'exists:users,id',
        ]);

        $applicationId = $request->application_id;
        $evaluatorIds = $request->evaluator_ids;

        DB::transaction(function () use ($applicationId, $evaluatorIds) {
            foreach ($evaluatorIds as $userId) {
                // Check if already assigned
                $exists = \App\Models\Evaluation::where('application_id', $applicationId)
                                    ->where('evaluator_id', $userId)
                                    ->exists();
                
                if (!$exists) {
                    \App\Models\Evaluation::create([
                        'application_id' => $applicationId,
                        'evaluator_id' => $userId,
                        'status' => 'pending',
                    ]);
                }
            }
        });

        return to_route('admin.applications.index')->with('success', 'Evaluadores asignados correctamente.');
    }

    /**
     * Remove an evaluator from a request.
     */
    public function removeEvaluator(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'evaluator_id' => 'required|exists:users,id',
        ]);

        \App\Models\Evaluation::where('application_id', $request->application_id)
                  ->where('evaluator_id', $request->evaluator_id)
                  ->delete();

        return back()->with('success', 'Evaluador removido.');
    }

    public function assignView($id)
    {
        $application = \App\Models\Application::with(['user.institution', 'announcement', 'evaluations.evaluator'])
            ->findOrFail($id);

        $evaluators = User::role('Evaluador')->select('id', 'name', 'email')->get();

        return Inertia::render('Admin/Applications/AssignEvaluator', [
            'application' => (new ApplicationResource($application))->resolve(),
            'evaluators' => $evaluators,
        ]);
    }

    /**
     * Display the specified request details + verdict form.
     */
    public function show($id)
    {
        $application = \App\Models\Application::with([
                'user.institution', 
                'user.priorityArea',
                'user.subArea',
                'evaluations.evaluator', 
                'documents', 
                'announcement'
            ])
            ->findOrFail($id);

        return Inertia::render('Admin/Applications/Show', [
            'application' => (new ApplicationResource($application))->resolve(),
        ]);
    }

    /**
     * Submit Admin Verdict (Approve/Reject).
     */
    public function verdict(Request $request, $id)
    {
        $application = \App\Models\Application::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'comentario' => 'required_if:status,rejected|nullable|string|max:1000',
        ]);

        $application->status = $validated['status'];
        if ($validated['status'] === 'rejected') {
            $application->admin_comment = $validated['comentario'];
        }
        $application->save();

        return to_route('admin.applications.show', $id)->with('success', 'Veredicto registrado correctamente.');
    }
}
