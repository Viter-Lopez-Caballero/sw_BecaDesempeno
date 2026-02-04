<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Models\Solicitud;
use App\Http\Resources\RequestControlSummaryResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function inicio(Request $request)
    {
        // Global Statistics
        $stats = [
            'total' => Solicitud::count(),
            'pending' => Solicitud::where('status', 'pending')->count(),
            'approved' => Solicitud::where('status', 'approved')->count(),
            'rejected' => Solicitud::where('status', 'rejected')->count(),
        ];

        // Top Institutions by Solicitudes Count (for chart/progress bars)
        $topInstitutions = Institucion::with('estado')
            ->withCount('solicitudes')
            ->having('solicitudes_count', '>', 0)
            ->orderByDesc('solicitudes_count')
            ->paginate(5, ['*'], 'top_page');

        // Main Table: Institutions with Approved/Rejected counts (filterable)
        $search = $request->input('search');
        $rows = $request->input('rows', 10);
        
        $institutions = Institucion::with('estado')
            ->withCount([
                'users as approved_count' => function ($query) {
                    $query->whereHas('solicitudes', function ($q) {
                        $q->where('status', 'approved');
                    });
                },
                'users as rejected_count' => function ($query) {
                    $query->whereHas('solicitudes', function ($q) {
                        $q->where('status', 'rejected');
                    });
                }
            ])
            ->when($search, function ($query, $search) {
                 $query->where('nombre', 'like', "%{$search}%")
                       ->orWhere('id', 'like', "%{$search}%");
            })
            ->whereHas('users.solicitudes', function($q) {
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
