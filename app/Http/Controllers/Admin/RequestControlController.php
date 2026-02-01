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
    public function index()
    {
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
            // Optimization: Only load institutions that have at least one request (optional, but good for summary)
            // or just list all. Let's list all for now or filter where approved_count + rejected_count > 0?
            // User requested "all applications of all teachers... but only shows accepted and rejected".
            // So we should probably filter institutions that have activity.
            ->whereHas('users.solicitudes', function($q) {
                $q->whereIn('status', ['approved', 'rejected']);
            })
            ->paginate(10);

        return Inertia::render('Admin/RequestControl/Index', [
            'institutions' => RequestControlSummaryResource::collection($institutions),
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
