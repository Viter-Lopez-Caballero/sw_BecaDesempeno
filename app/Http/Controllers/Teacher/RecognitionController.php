<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Recognition;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\PdfGenerationService;

class RecognitionController extends Controller
{
    protected PdfGenerationService $pdfGenerationService;

    public function __construct(PdfGenerationService $pdfGenerationService)
    {
        $this->pdfGenerationService = $pdfGenerationService;

        $this->middleware("permission:teacher.recognitions.index")->only(['index']);
    }

    /**
     * Display a listing of the teacher's recognitions.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $sortField = $request->input('sort_field', 'sent_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $sortColumn = match ($sortField) {
            'id' => 'recognitions.id',
            'announcement' => 'announcements.name',
            'sent_at' => 'sent_at',
            default => 'sent_at',
        };

        // Obtener los reconocimientos del docente actual, donde activos
        $recognitions = Recognition::with('announcement')
            ->leftJoin('announcements', 'recognitions.announcement_id', '=', 'announcements.id')
            ->select('recognitions.*')
            ->where('user_id', $user->id)
            ->where('recognitions.active', true)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(10);

        return Inertia::render('Teacher/Recognitions/Index', [
            'recognitions' => $recognitions,
            'filters' => $request->all(['page', 'search', 'sort_field', 'sort_direction']),
        ]);
    }

    /**
     * Download the specified recognition for the teacher.
     */
    public function download(Request $request, Recognition $recognition)
    {
        $user = $request->user();

        // Asegurar que el reconocimiento pertenece al docente actual
        if ($recognition->user_id !== $user->id || !$recognition->active) {
            abort(403, 'No tienes permiso para descargar este reconocimiento o no está activo.');
        }

        // Ensure relations are loaded
        $recognition->loadMissing('announcement');

        return $this->pdfGenerationService->generateTeacherRecognitionPdf($recognition, $user);
    }
}
