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

        // Obtener los reconocimientos del docente actual, donde activos
        $recognitions = Recognition::with('announcement')
            ->where('user_id', $user->id)
            ->where('active', true)
            ->orderBy('sent_at', 'desc')
            ->paginate(10);

        return Inertia::render('Teacher/Recognitions/Index', [
            'recognitions' => $recognitions,
            'filters' => $request->all(['page']),
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
