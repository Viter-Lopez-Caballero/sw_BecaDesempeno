<?php

namespace App\Services;

use App\Mail\EvaluatorAssigned;
use App\Mail\ApplicationVerdict;
use App\Mail\AnnouncementStageChange;
use App\Mail\AnnouncementDateChange;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send notification when evaluations are assigned to an evaluator
     */
    public function notifyEvaluatorAssignment($evaluatorId, $evaluationsCount)
    {
        $evaluator = User::findOrFail($evaluatorId);

        // Create notification
        Notification::create([
            'title' => 'Nuevas Evaluaciones Asignadas',
            'data' => [
                'message' => 'Se te han asignado nuevas evaluaciones.',
                'count' => $evaluationsCount,
            ],
            'type' => 'evaluator_assignment',
            'user_id' => $evaluatorId,
        ]);

        // Send email
        Mail::to($evaluator->email)->queue(new EvaluatorAssigned(
            $evaluator->name,
            $evaluationsCount,
            5 // days limit
        ));
    }

    /**
     * Send notification when an application verdict is made
     */
    public function notifyApplicationVerdict($applicationId, $teacherId, $status, $announcementTitle)
    {
        $teacher = User::findOrFail($teacherId);
        $statusText = $status === 'approved' ? 'aprobada' : 'no aceptada';

        // Create notification
        Notification::create([
            'title' => $status === 'approved' ? 'Solicitud Aprobada' : 'Resultado de tu Solicitud',
            'data' => [
                'message' => "Tu solicitud ha sido {$statusText}.",
                'status' => $status,
                'announcement' => $announcementTitle,
            ],
            'type' => 'application_verdict',
            'user_id' => $teacherId,
        ]);

        // Send email
        Mail::to($teacher->email)->queue(new ApplicationVerdict(
            $teacher->name,
            $status,
            $announcementTitle
        ));
    }

    /**
     * Send notification when announcement stage changes
     */
    public function notifyAnnouncementStageChange($announcementId, $announcementTitle, $newStage, $stageDate = null)
    {
        // Get all users with roles: Admin, Evaluador, Docente
        $users = User::role(['Admin', 'Evaluador', 'Docente'])->get();

        foreach ($users as $user) {
            // Create notification
            Notification::create([
                'title' => 'Cambio de Etapa en Convocatoria',
                'data' => [
                    'message' => "La convocatoria '{$announcementTitle}' ha cambiado a la etapa: {$newStage}",
                    'stage' => $newStage,
                    'date' => $stageDate,
                ],
                'type' => 'announcement_stage_change',
                'user_id' => $user->id,
            ]);

            // Send email
            Mail::to($user->email)->queue(new AnnouncementStageChange(
                $user->name,
                $announcementTitle,
                $newStage,
                $stageDate
            ));
        }
    }

    /**
     * Send notification when announcement dates change
     */
    public function notifyAnnouncementDateChange($announcementId, $announcementTitle, $changes)
    {
        // Get all users with roles: Admin, Evaluador, Docente
        $users = User::role(['Admin', 'Evaluador', 'Docente'])->get();

        $changesText = implode(', ', array_column($changes, 'label'));

        foreach ($users as $user) {
            // Create notification
            Notification::create([
                'title' => 'Cambio de Fechas en Convocatoria',
                'data' => [
                    'message' => "Se han actualizado las fechas de la convocatoria '{$announcementTitle}'",
                    'changes' => $changesText,
                ],
                'type' => 'announcement_date_change',
                'user_id' => $user->id,
            ]);

            // Send email
            Mail::to($user->email)->queue(new AnnouncementDateChange(
                $user->name,
                $announcementTitle,
                $changes
            ));
        }
    }
}
