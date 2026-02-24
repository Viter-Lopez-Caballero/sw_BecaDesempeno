<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\RemoveEvaluatorRequest;
use Illuminate\Support\Facades\DB;
use App\Traits\Filterable;
use Inertia\Inertia;
use App\Services\NotificationService;

class ApplicationController extends Controller
{
    use Filterable;

    protected \App\Services\AssignmentService $assignmentService;
    protected NotificationService $notificationService;

    public function __construct(\App\Services\AssignmentService $assignmentService, NotificationService $notificationService)
    {
        $this->assignmentService = $assignmentService;
        $this->notificationService = $notificationService;
    }

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
        $evaluators = User::role('Evaluador')->with('institution')->get();

        return Inertia::render('Admin/Applications/Index', [
            'applications' => ApplicationResource::collection($applications),
            'evaluators' => $evaluators,
            'filters' => $filters,
        ]);
    }

    /**
     * Assign evaluators to a request.
     */
    public function assignEvaluator(\App\Http\Requests\AssignEvaluatorRequest $request)
    {
        $this->assignmentService->assignEvaluators(
            $request->application_id,
            $request->evaluator_ids
        );

        return to_route('admin.applications.index')->with('success', 'Evaluadores asignados correctamente.');
    }

    /**
     * Remove an evaluator from a request.
     */
    public function removeEvaluator(RemoveEvaluatorRequest $request)
    {
        $this->assignmentService->removeEvaluator(
            $request->application_id,
            $request->evaluator_id
        );

        return back()->with('success', 'Evaluador removido.');
    }

    public function assignView($id)
    {
        $application = \App\Models\Application::with([
            'user.institution',
            'user.priorityArea',
            'user.subArea',
            'announcement',
            'evaluations.evaluator'
        ])
            ->findOrFail($id);

        $evaluators = User::role('Evaluador')->with('institution')->get();

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
    public function verdict(\App\Http\Requests\SubmitAdminVerdictRequest $request, $id)
    {
        $application = \App\Models\Application::findOrFail($id);

        $validated = $request->validated();

        $application->status = $validated['status'];
        if ($validated['status'] === 'rejected') {
            $application->admin_comment = $validated['comentario'];
        }
        $application->save();

        if ($validated['status'] === 'approved') {
            \App\Models\Recognition::firstOrCreate([
                'user_id' => $application->user_id,
                'announcement_id' => $application->announcement_id,
            ], [
                'active' => true,
                'sent_at' => now(),
            ]);
        }

        // Enviar notificación al docente
        $this->notificationService->notifyApplicationVerdict(
            $application->id,
            $application->user_id,
            $validated['status'],
            $application->announcement->name // Use 'name' instead of 'title' as seen in model
        );

        return to_route('admin.applications.index')->with('success', 'Veredicto registrado correctamente.');
    }

    /**
     * View a specific evaluation responses in Read-Only mode.
     */
    public function showEvaluation($application_id, $evaluation_id)
    {
        $application = \App\Models\Application::with([
            'user.institution',
            'user.priorityArea',
            'user.subArea',
            'announcement'
        ])->findOrFail($application_id);

        $evaluation = \App\Models\Evaluation::with('evaluator')
            ->where('application_id', $application_id)
            ->findOrFail($evaluation_id);

        // Fetch active rubric
        $rubric = \App\Models\Rubric::with(['questions.options'])
            ->where('is_active', true)
            ->first();

        // Pass structured view similarly to Evaluator view but strictly read only
        return Inertia::render('Admin/Applications/EvaluationView', [
            'application' => (new ApplicationResource($application))->resolve(),
            'evaluation' => $evaluation,
            'rubric' => $rubric,
            'teacher' => $application->user,
        ]);
    }
}
