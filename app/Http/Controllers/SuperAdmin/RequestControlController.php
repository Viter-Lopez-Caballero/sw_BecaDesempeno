<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Application;
use App\Http\Resources\RequestControlSummaryResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RequestControlController extends Controller
{
    public function index(Request $request)
    {
        // Global Statistics
        $stats = [
            'total' => Application::count(),
            'pending' => Application::where('status', 'pending')->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];

        // Main Table: Institutions with Approved/Rejected counts (filterable)
        $search = $request->input('search');
        $estado = $request->input('estado');
        $rows = $request->input('rows', 10);
        
        $institutions = Institution::with('state')
            ->withCount([
                'users as approved_count' => function ($query) {
                    $query->whereHas('applications', function ($q) {
                        $q->where('status', 'approved');
                    });
                },
                'users as rejected_count' => function ($query) {
                    $query->whereHas('applications', function ($q) {
                        $q->where('status', 'rejected');
                    });
                }
            ])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
            })
            ->when($estado, function ($query, $estado) {
                $query->whereHas('state', function ($q) use ($estado) {
                    $q->where('name', $estado);
                });
            })
            ->whereHas('users.applications', function($q) {
                $q->whereIn('status', ['approved', 'rejected']);
            })
            ->paginate($rows)
            ->withQueryString();

        // Get unique states for filter dropdown
        $estados = \App\Models\State::orderBy('name')->get(['id', 'name']);

        return Inertia::render('SuperAdmin/Applications/Index', [
            'stats' => $stats,
            'institutions' => RequestControlSummaryResource::collection($institutions),
            'states' => $estados,
            'filters' => $request->all(['search', 'estado', 'rows']),
        ]);
    }
}
