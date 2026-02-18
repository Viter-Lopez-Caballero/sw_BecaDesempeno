<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Evaluation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RemoveExpiredEvaluations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evaluations:remove-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina asignaciones de evaluaciones pendientes que tengan más de 5 días';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limitDate = Carbon::now()->subDays(5);

        $this->info("Buscando evaluaciones pendientes creadas antes de: {$limitDate->toDateTimeString()}");
        Log::info("Cron Evaluaciones: Iniciando eliminación de evaluaciones vencidas para fecha: {$limitDate->toDateTimeString()}");

        // Find pending evaluations older than 5 days
        $expiredEvaluations = Evaluation::where('status', 'pending')
            ->where('created_at', '<', $limitDate)
            ->get();

        if ($expiredEvaluations->isEmpty()) {
            $this->info('No se encontraron evaluaciones vencidas.');
            Log::info('Cron Evaluaciones: No hay evaluaciones vencidas.');
            return;
        }

        $count = $expiredEvaluations->count();
        $this->info("Se encontraron {$count} evaluaciones vencidas. Eliminando...");

        foreach ($expiredEvaluations as $evaluation) {
            Log::info("Cron Evaluaciones: Eliminando evaluación ID {$evaluation->id} - Evaluador: {$evaluation->evaluator_id} - Solicitud: {$evaluation->application_id}");
            $evaluation->delete();
        }

        $this->info("✓ Se eliminaron {$count} evaluaciones vencidas correctamente.");
        Log::info("Cron Evaluaciones: Se eliminaron {$count} evaluaciones vencidas.");

        return Command::SUCCESS;
    }
}
