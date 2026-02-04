<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RequestControlSummaryResource;
use App\Http\Resources\SolicitudResource;
use App\Models\Institucion;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RequestControlController extends Controller
{
    /**
     * Display a listing of institutions with approved/rejected counts.
     */
    public function index(Request $request)
    {
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
            ->paginate($rows)
            ->withQueryString();

        return Inertia::render('Admin/RequestControl/Index', [
            'institutions' => RequestControlSummaryResource::collection($institutions),
            'filters' => $request->all(['search', 'rows']),
        ]);
    }

    /**
     * Display the specified resource details (list of requests for a campus).
     */
    public function show($id)
    {
        $institution = Institucion::with('estado')->findOrFail($id);

        $solicitudes = Solicitud::whereHas('user', function ($q) use ($id) {
                $q->where('institucion_id', $id);
            })
            ->whereIn('status', ['approved', 'rejected'])
            ->with(['user', 'convocatoria'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return Inertia::render('Admin/RequestControl/Show', [
            'institution' => $institution,
            'solicitudes' => SolicitudResource::collection($solicitudes),
        ]);
    }
}
