<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reconocimiento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReconocimientoController extends Controller
{
    /**
     * Display a listing of evaluators with their convocatorias.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $rows = $request->input('rows', 10);

        // Query para obtener evaluadores con sus convocatorias donde participaron
        // JOIN: evaluaciones -> solicitudes -> convocatorias
        $reconocimientos = DB::table('evaluaciones')
            ->join('users', 'evaluaciones.user_id', '=', 'users.id')
            ->join('solicitudes', 'evaluaciones.solicitud_id', '=', 'solicitudes.id')
            ->join('convocatorias', 'solicitudes.convocatoria_id', '=', 'convocatorias.id')
            ->leftJoin('reconocimientos', function ($join) {
                $join->on('reconocimientos.user_id', '=', 'users.id')
                     ->on('reconocimientos.convocatoria_id', '=', 'convocatorias.id');
            })
            ->select(
                'users.id as evaluador_id',
                'users.name as evaluador_nombre',
                'convocatorias.id as convocatoria_id',
                'convocatorias.nombre as convocatoria_nombre',
                'convocatorias.created_at as convocatoria_fecha',
                DB::raw('COUNT(DISTINCT evaluaciones.id) as solicitudes_revisadas'),
                'reconocimientos.id as reconocimiento_id',
                DB::raw('COALESCE(reconocimientos.activo, 0) as activo'),
                'reconocimientos.enviado_at'
            )
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', "%{$search}%")
                      ->orWhere('convocatorias.nombre', 'like', "%{$search}%");
                });
            })
            ->groupBy(
                'users.id',
                'users.name',
                'convocatorias.id',
                'convocatorias.nombre',
                'convocatorias.created_at',
                'reconocimientos.id',
                'reconocimientos.activo',
                'reconocimientos.enviado_at'
            )
            ->orderBy('convocatorias.created_at', 'desc') // Más recientes primero
            ->orderBy('users.name', 'asc')
            ->paginate($rows)
            ->withQueryString();

        return Inertia::render('Admin/Reconocimientos/Index', [
            'reconocimientos' => $reconocimientos,
            'filters' => $request->all(['search', 'rows']),
        ]);
    }

    /**
     * Toggle reconocimiento status.
     */
    public function toggle(Request $request)
    {
        // $id es el reconocimiento_id si existe, o necesitamos user_id + convocatoria_id
        // Mejor recibir user_id y convocatoria_id en el request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'convocatoria_id' => 'required|exists:convocatorias,id',
        ]);

        $userId = $request->user_id;
        $convocatoriaId = $request->convocatoria_id;

        // Verificar que el evaluador haya revisado al menos 1 solicitud de esta convocatoria
        $solicitudesRevisadas = DB::table('evaluaciones')
            ->join('solicitudes', 'evaluaciones.solicitud_id', '=', 'solicitudes.id')
            ->where('evaluaciones.user_id', $userId)
            ->where('solicitudes.convocatoria_id', $convocatoriaId)
            ->count();

        if ($solicitudesRevisadas === 0) {
            return back()->withErrors(['error' => 'El evaluador no ha revisado solicitudes de esta convocatoria.']);
        }

        // Buscar o crear reconocimiento
        $reconocimiento = Reconocimiento::firstOrNew([
            'user_id' => $userId,
            'convocatoria_id' => $convocatoriaId,
        ]);

        // Toggle estado
        $reconocimiento->activo = !$reconocimiento->activo;
        
        // Si se activa, guardar fecha de envío
        if ($reconocimiento->activo) {
            $reconocimiento->enviado_at = now();
        }

        $reconocimiento->save();

        return back()->with('success', $reconocimiento->activo 
            ? 'Reconocimiento activado correctamente.' 
            : 'Reconocimiento desactivado correctamente.');
    }
}
