<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Convocatoria;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateConvocatoriaStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convocatoria:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el estado de las convocatorias basado en las fechas del calendario';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today(); // 00:00:00 of today

        $this->info("Ejecutando actualización de estados para fecha: {$today->toDateString()}");
        Log::info("Cron Convocatorias: Iniciando revisión para {$today->toDateString()}");

        // 1. Activar convocatorias pendientes cuya fecha de publicación ya llegó
        // Buscamos convocatorias pendientes que tengan calendario con publicacion_inicio <= hoy
        $pendientes = Convocatoria::where('estado', 'pendiente')
            ->whereHas('calendario', function ($query) use ($today) {
                $query->where('publicacion_inicio', '<=', $today);
            })
            ->with('calendario')
            ->get();

        foreach ($pendientes as $convocatoria) {
            DB::transaction(function () use ($convocatoria) {
                // Desactivar cualquier otra activa para mantener regla de "Solo una activa"
                Convocatoria::where('estado', 'activa')
                    ->where('id', '!=', $convocatoria->id)
                    ->update(['estado' => 'cerrada']);

                $convocatoria->update(['estado' => 'activa']);
                
                $this->info("Convocatoria activada: {$convocatoria->nombre}");
                Log::info("Cron Convocatorias: Convocatoria ID {$convocatoria->id} activada.");
            });
        }

        // 2. Cerrar convocatorias activas cuya fecha de resultados ya pasó
        // Buscamos activas que tengan calendario con resultados_fin < hoy
        // (Es decir, ayer fue el último día)
        $activas = Convocatoria::where('estado', 'activa')
            ->whereHas('calendario', function ($query) use ($today) {
                $query->where('resultados_fin', '<', $today);
            })
            ->get();

        foreach ($activas as $convocatoria) {
            $convocatoria->update(['estado' => 'cerrada']);
            $this->info("Convocatoria cerrada: {$convocatoria->nombre}");
            Log::info("Cron Convocatorias: Convocatoria ID {$convocatoria->id} cerrada (finalizó periodo).");
        }

        $this->info('Actualización completada.');
    }
}
