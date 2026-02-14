<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\Institution;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $institucionId = $request->input('institucion_id');
        $estadoId = $request->input('estado_id');

        // Base Query for Stats
        $query = Application::query()
            ->join('users', 'applications.user_id', '=', 'users.id')
            ->join('institutions', 'users.institution_id', '=', 'institutions.id')
            ->join('states', 'institutions.state_id', '=', 'states.id');

        // Apply Filters
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('institutions.name', 'like', "%{$search}%")
                  ->orWhere('states.name', 'like', "%{$search}%");
            });
        }

        if ($institucionId) {
            $query->where('institutions.id', $institucionId);
        }

        if ($estadoId) {
            $query->where('states.id', $estadoId);
        }

        // --- Stats Cards ---
        // Clone query for each count to avoid interference
        $totalSolicitudes = (clone $query)->count();
        $totalAprobadas = (clone $query)->where('applications.status', 'approved')->count();
        // Rejected could serve for both 'rejected' status or combined (if design implies completed-rejected)
        // Assuming strict 'rejected' status based on user request "Total rechazadas"
        $totalRechazadas = (clone $query)->where('applications.status', 'rejected')->count();


        // --- Chart Data ---
        // Group by Institution to show bars per campus
        // Select: Institution Name, Count Approved, Count Rejected
        $chartData = (clone $query)
            ->select(
                'institutions.name as institution',
                DB::raw("SUM(CASE WHEN applications.status = 'approved' THEN 1 ELSE 0 END) as aprobadas"),
                DB::raw("SUM(CASE WHEN applications.status = 'rejected' THEN 1 ELSE 0 END) as rechazadas")
            )
            ->groupBy('institutions.id', 'institutions.name')
            ->orderBy('institutions.name')
            ->get();

        // Format for Chart.js
        // Labels: Institution Names
        // Dataset 1: Approved
        // Dataset 2: Rejected
        $labels = $chartData->pluck('institution');
        $dataAprobadas = $chartData->pluck('aprobadas');
        $dataRechazadas = $chartData->pluck('rechazadas');


        return Inertia::render('SuperAdmin/Dashboard/Index', [
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
            'institutions' => Institution::select('id', 'name')->orderBy('name')->get(),
            'states' => State::select('id', 'name')->orderBy('name')->get(),
        ]);
    }
}
