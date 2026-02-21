<?php

namespace App\Services;

use App\Models\Evaluation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class AssignmentService
{
    /**
     * Asigna uno o más evaluadores a una solicitud específica en una transacción.
     *
     * @param int $applicationId El ID de la Application.
     * @param array $evaluatorIds Un arreglo de IDs de User (rol Evaluador).
     * @return void
     */
    public function assignEvaluators(int $applicationId, array $evaluatorIds): void
    {
        DB::transaction(function () use ($applicationId, $evaluatorIds) {
            foreach ($evaluatorIds as $userId) {
                // Verificamos que no exista una evaluación previa para ese dúo
                $exists = Evaluation::where('application_id', $applicationId)
                                    ->where('evaluator_id', $userId)
                                    ->exists();
                
                if (!$exists) {
                    Evaluation::create([
                        'application_id' => $applicationId,
                        'evaluator_id' => $userId,
                        'status' => 'pending',
                        'deadline_at' => Carbon::now()->addWeekdays(7),
                    ]);
                }
            }
        });
    }

    /**
     * Remueve a un evaluador de una solicitud específica.
     *
     * @param int $applicationId
     * @param int $evaluatorId
     * @return void
     */
    public function removeEvaluator(int $applicationId, int $evaluatorId): void
    {
        Evaluation::where('application_id', $applicationId)
                  ->where('evaluator_id', $evaluatorId)
                  ->delete();
    }
}
