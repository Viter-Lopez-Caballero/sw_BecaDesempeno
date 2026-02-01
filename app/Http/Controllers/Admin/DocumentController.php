<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

use App\Http\Resources\SolicitudResource;

class DocumentController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::with(['user.institucion', 'convocatoria'])
            ->withCount('documentos')
            ->orderByDesc('created_at')
            ->paginate(10);

        return Inertia::render('Admin/Documents/Index', [
            'solicitudes' => SolicitudResource::collection($solicitudes),
        ]);
    }

    public function show($id)
    {
        $solicitud = Solicitud::with(['user.institucion', 'user.priorityArea', 'user.subArea', 'convocatoria', 'documentos'])
            ->findOrFail($id);

        return Inertia::render('Admin/Documents/Show', [
            'solicitud' => new SolicitudResource($solicitud),
        ]);
    }

    public function download(Documento $documento)
    {
        // For dummy files in seeding, we might need a visual check or ensure they exist.
        // In production, this would serve valid files.
        if (!Storage::exists($documento->file_path)) {
            // Fallback for demo purposes if using dummy paths
            return back()->with('error', 'El archivo no existe.');
        }

        return Storage::download($documento->file_path, $documento->name);
    }
}
