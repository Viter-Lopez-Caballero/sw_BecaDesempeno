<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Convocatoria;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateAnnouncementStatus extends Command
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
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today(); // 00:00:00 of today

        $this->info("Ejecutando actualización de estados para fecha: {$today->toDateString()}");
        Log::info("Cron Convocatorias: Iniciando revisión para {$today->toDateString()}");

        // 1. Activar convocatorias pendientes cuya fecha de publicación ya llegó
        // Buscamos convocatorias pendientes que tengan calendario con publication_start <= hoy
        $pendientes = \App\Models\Announcement::where('status', 'pendiente')
            ->whereHas('calendar', function ($query) use ($today) {
                $query->where('publication_start', '<=', $today);
            })
            ->with('calendar')
            ->get();

        foreach ($pendientes as $announcement) {
            DB::transaction(function () use ($announcement) {
                // Desactivar cualquier otra activa para mantener regla de "Solo una activa"
                \App\Models\Announcement::where('status', 'activa')
                    ->where('id', '!=', $announcement->id)
                    ->update(['status' => 'cerrada']);

                $announcement->update(['status' => 'activa']);
                
                $this->info("Convocatoria activada: {$announcement->name}");
                Log::info("Cron Convocatorias: Convocatoria ID {$announcement->id} activada.");
            });
        }

        // 2. Cerrar convocatorias activas cuya fecha de resultados ya pasó
        // Buscamos activas que tengan calendario con results_end < hoy
        // (Es decir, ayer fue el último día)
        $activas = \App\Models\Announcement::where('status', 'activa')
            ->whereHas('calendar', function ($query) use ($today) {
                $query->where('results_end', '<', $today);
            })
            ->get();

        foreach ($activas as $announcement) {
            $announcement->update(['status' => 'cerrada']);
            $this->info("Convocatoria cerrada: {$announcement->name}");
            Log::info("Cron Convocatorias: Convocatoria ID {$announcement->id} cerrada (finalizó periodo).");
        }

        $this->info('Actualización completada.');
    }
}
