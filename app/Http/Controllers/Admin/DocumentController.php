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
    public function index(Request $request)
    {
        $search = $request->input('search');
        $rows = $request->input('rows', 10);
        $order = $request->input('order', 'created_at');
        $direction = $request->input('direction', 'desc');

        $solicitudes = Solicitud::with(['user.institucion', 'convocatoria'])
            ->withCount('documentos')
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('convocatoria', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                })
                ->orWhere('id', 'like', "%{$search}%");
            })
            ->when($order, function ($query, $order) use ($direction) {
                // Handle relationship sorting if needed, but basic sorting first
                if (in_array($order, ['id', 'created_at'])) {
                    $query->orderBy($order, $direction);
                }
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate($rows)
            ->withQueryString();

        return Inertia::render('Admin/Documents/Index', [
            'solicitudes' => SolicitudResource::collection($solicitudes),
            'filters' => $request->all(['search', 'rows', 'order', 'direction']),
        ]);
    }

    public function show($id)
    {
        $solicitud = Solicitud::with(['user.institucion', 'user.priorityArea', 'user.subArea', 'convocatoria', 'documentos'])
            ->findOrFail($id);

        return Inertia::render('Admin/Documents/Show', [
            'solicitud' => (new SolicitudResource($solicitud))->resolve(),
        ]);
    }

    public function download(Documento $documento)
    {
        // For dummy files in seeding, we might need a visual check or ensure they exist.
        // In production, this would serve valid files.
        if (!Storage::disk('public')->exists($documento->file_path)) {
            // Fallback for demo purposes if using dummy paths
            return back()->with('error', 'El archivo no existe.');
        }

        return Storage::disk('public')->download($documento->file_path, $documento->name);
    }

    public function stream(Documento $documento)
    {
        // Add check if admin can view this document?
        // Documents are generally viewable by admin if they can view the solicitud.
        // Assuming Middleware handles Solicitud permissions, checking file existence is enough here.

        if (!Storage::disk('public')->exists($documento->file_path)) {
            return back()->with('error', 'El archivo no existe.');
        }

        return response()->file(Storage::disk('public')->path($documento->file_path));
    }
}
