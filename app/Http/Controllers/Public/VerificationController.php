<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Recognition;

class VerificationController extends Controller
{
    public function verify($identifier)
    {
        $recognition = Recognition::validByIdentifier($identifier)->with('announcement')->first();

        if (!$recognition) {
            abort(404, 'Reconocimiento no encontrado o identificador inválido.');
        }

        $announcementName = $recognition->convocatoria_nombre 
            ?? ($recognition->announcement ? $recognition->announcement->name : 'CONVOCATORIA GENERAL');

        $snapshot = is_string($recognition->snapshot_data) ? json_decode($recognition->snapshot_data, true) : $recognition->snapshot_data;
        $directorName = $snapshot['director_name'] ?? 'Víctor Vázquez López';
        $directorTitle = $snapshot['director_title'] ?? 'Director General';

        return Inertia::render('Public/VerifyRecognition', [
            'identifier' => $recognition->identifier,
            'participant' => $recognition->user ? mb_strtoupper($recognition->user->name) : 'PARTICIPANTE DESCONOCIDO',
            'announcementName' => mb_strtoupper($announcementName),
            'digitalSeal' => $recognition->digital_seal,
            'directorName' => mb_strtoupper($directorName),
            'directorTitle' => $directorTitle,
        ]);
    }
}
