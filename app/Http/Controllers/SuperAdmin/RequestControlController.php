<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Institucion;
use App\Models\Solicitud;
use App\Http\Resources\RequestControlSummaryResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RequestControlController extends Controller
{
    public function index(Request $request)
    {
        // Global Statistics
        $stats = [
            'total' => Solicitud::count(),
            'pending' => Solicitud::where('status', 'pending')->count(),
            'approved' => Solicitud::where('status', 'approved')->count(),
            'rejected' => Solicitud::where('status', 'rejected')->count(),
        ];

        // Main Table: Institutions with Approved/Rejected counts (filterable)
        $search = $request->input('search');
        $estado = $request->input('estado');
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
            ->when($estado, function ($query, $estado) {
                $query->whereHas('estado', function ($q) use ($estado) {
                    $q->where('nombre', $estado);
                });
            })
            ->whereHas('users.solicitudes', function($q) {
                $q->whereIn('status', ['approved', 'rejected']);
            })
            ->paginate($rows)
            ->withQueryString();

        // Get unique estados for filter dropdown
        $estados = \App\Models\Estado::orderBy('nombre')->get(['id', 'nombre']);

        return Inertia::render('SuperAdmin/Solicitudes/Index', [
            'stats' => $stats,
            'institutions' => RequestControlSummaryResource::collection($institutions),
            'estados' => $estados,
            'filters' => $request->all(['search', 'estado', 'rows']),
        ]);
    }
}
