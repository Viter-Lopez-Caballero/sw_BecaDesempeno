<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Application;
use App\Http\Resources\RequestControlSummaryResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Global Statistics
        $stats = [
            'total' => \App\Models\Application::count(),
            'pending' => \App\Models\Application::where('status', 'pending')->count(),
            'approved' => \App\Models\Application::where('status', 'approved')->count(),
            'rejected' => \App\Models\Application::where('status', 'rejected')->count(),
        ];

        // Top Institutions by Applications Count (for chart/progress bars)
        $topInstitutions = \App\Models\Institution::with('state')
            ->withCount('applications')
            ->having('applications_count', '>', 0)
            ->orderByDesc('applications_count')
            ->paginate(5, ['*'], 'top_page');

        // Main Table: Institutions with Approved/Rejected counts (filterable)
        $search = $request->input('search');
        $rows = $request->input('rows', 10);
        
        $institutions = \App\Models\Institution::with('state')
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
            ->whereHas('users.applications', function($q) { // users.applications is likely correct via HasManyThrough or similar? No, User hasMany Applications. Institution hasMany Users. So Institution hasManyThrough Applications? Or just check users with applications.
                // Original was `users.solicitudes`. User model has `applications`.
                $q->whereIn('status', ['approved', 'rejected']);
            })
            ->paginate($rows, ['*'], 'table_page')
            ->withQueryString();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'topInstitutions' => $topInstitutions,
            'institutions' => RequestControlSummaryResource::collection($institutions),
            'filters' => $request->all(['search', 'rows']),
        ]);
    }
}
