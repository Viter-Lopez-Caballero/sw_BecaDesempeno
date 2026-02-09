<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Estado;
use App\Models\Institucion;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InicioController extends Controller
{
    public function inicio(Request $request)
    {
        $search = $request->input('search');
        $institucionId = $request->input('institucion_id');
        $estadoId = $request->input('estado_id');

        // Base Query for Stats
        $query = Solicitud::query()
            ->join('users', 'solicitudes.user_id', '=', 'users.id')
            ->join('instituciones', 'users.institucion_id', '=', 'instituciones.id')
            ->join('estados', 'instituciones.estado_id', '=', 'estados.id');

        // Apply Filters
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('instituciones.nombre', 'like', "%{$search}%")
                  ->orWhere('estados.nombre', 'like', "%{$search}%");
            });
        }

        if ($institucionId) {
            $query->where('instituciones.id', $institucionId);
        }

        if ($estadoId) {
            $query->where('estados.id', $estadoId);
        }

        // --- Stats Cards ---
        // Clone query for each count to avoid interference
        $totalSolicitudes = (clone $query)->count();
        $totalAprobadas = (clone $query)->where('solicitudes.status', 'approved')->count();
        // Rejected could serve for both 'rejected' status or combined (if design implies completed-rejected)
        // Assuming strict 'rejected' status based on user request "Total rechazadas"
        $totalRechazadas = (clone $query)->where('solicitudes.status', 'rejected')->count();


        // --- Chart Data ---
        // Group by Institution to show bars per campus
        // Select: Institution Name, Count Approved, Count Rejected
        $chartData = (clone $query)
            ->select(
                'instituciones.nombre as institucion',
                DB::raw("SUM(CASE WHEN solicitudes.status = 'approved' THEN 1 ELSE 0 END) as aprobadas"),
                DB::raw("SUM(CASE WHEN solicitudes.status = 'rejected' THEN 1 ELSE 0 END) as rechazadas")
            )
            ->groupBy('instituciones.id', 'instituciones.nombre')
            ->orderBy('instituciones.nombre')
            ->get();

        // Format for Chart.js
        // Labels: Institution Names
        // Dataset 1: Approved
        // Dataset 2: Rejected
        $labels = $chartData->pluck('institucion');
        $dataAprobadas = $chartData->pluck('aprobadas');
        $dataRechazadas = $chartData->pluck('rechazadas');


        return Inertia::render('SuperAdmin/Inicio/Index', [
            'stats' => [
                'total' => $totalSolicitudes,
                'approved' => $totalAprobadas,
                'rejected' => $totalRechazadas,
            ],
            'chart' => [
                'labels' => $labels,
                'approved' => $dataAprobadas,
                'rejected' => $dataRechazadas,
            ],
            'filters' => [
                'search' => $search,
                'institucion_id' => $institucionId,
                'estado_id' => $estadoId,
            ],
            'instituciones' => Institucion::select('id', 'nombre')->orderBy('nombre')->get(),
            'estados' => Estado::select('id', 'nombre')->orderBy('nombre')->get(),
        ]);
    }
}
